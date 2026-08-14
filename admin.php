<?php
session_start();
include "dbconnect.php";

/* hardcoded admins, check if admin or user if not then show login page */
$ADMINS = ["apichart123", "krystal123"];
$user   = $_SESSION['valid_user'] ?? null;
if (!in_array($user, $ADMINS, true)) {
  header('Location: login.php?msg=' . urlencode('Admins only.'));
  exit;
}

/* helper functions: trimming and row fetching */
function trim_val($key, $src)
{ return trim($src[$key] ?? ''); }

function trim_string($string)
{
  $string = trim($string ?? '');
  return $string === '' ? 'N/A' : $string;
}

function fetch_all_rows(mysqli_result $result)
{
  $rows = [];
  while ($r = $result->fetch_assoc()) { $rows[] = $r; }
  return $rows;
}

/* for messages and edit */
$movie_msg = '';
$movie_editing = null;

$member_msg = '';
$member_editing = null;

$showtime_msg = '';
$showtime_editing = null;

/* POST handler */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  /* MOVIES edit*/
  if ($action === 'movie_delete') {
    $id = (int)($_POST['moviesid'] ?? 0);
    if ($id > 0) {
      $statement = $dbconnect->prepare("DELETE FROM movies WHERE moviesid=?");
      $statement->bind_param("i", $id);
      $statement->execute();
      $statement->close();
      $movie_msg = "Movie deleted.";
    }
  }

  if ($action === 'movie_add' || $action === 'movie_update') {
    $id          = (int)($_POST['moviesid'] ?? 0);
    $movie_name  = trim_val('movie_name', $_POST);
    $synopsis    = trim_val('synopsis', $_POST);
    $main_cast   = trim_val('main_cast', $_POST);
    $language    = trim_val('language', $_POST);
    $genre       = trim_val('genre', $_POST);
    $runtime     = trim_val('runtime', $_POST);
    $rating      = trim_val('rating', $_POST);
    $poster_path = trim_val('poster_path', $_POST);
    $release     = trim_val('release_date', $_POST);

    if ($movie_name === '' || $synopsis === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $release)) {
      $movie_msg = "Required: Movie Name, Synopsis, valid Release Date (YYYY-MM-DD).";
    } else {
      if ($action === 'movie_add') {
        $statement = $dbconnect->prepare(
          "INSERT INTO movies
           (movie_name, synopsis, main_cast, `language`, release_date, genre, runtime, rating, poster_path)
           VALUES (?,?,?,?,?,?,?,?,?)"
        );
        $statement->bind_param(
          "sssssssss",
          $movie_name,
          $synopsis,
          trim_string($main_cast),
          trim_string($language),
          $release,
          trim_string($genre),
          trim_string($runtime),
          trim_string($rating),
          trim_string($poster_path)
        );
        $ok = $statement->execute();
        $statement->close();
        $movie_msg = $ok ? "Movie added." : "Movie save failed.";
      } else {
        $statement = $dbconnect->prepare(
          "UPDATE movies SET
            movie_name=?, synopsis=?, main_cast=?, `language`=?, release_date=?, genre=?, runtime=?, rating=?, poster_path=?
           WHERE moviesid=?"
        );
        $statement->bind_param(
          "sssssssssi",
          $movie_name,
          $synopsis,
          trim_string($main_cast),
          trim_string($language),
          $release,
          trim_string($genre),
          trim_string($runtime),
          trim_string($rating),
          trim_string($poster_path),
          $id
        );
        $ok = $statement->execute();
        $statement->close();
        $movie_msg = $ok ? "Movie updated." : "Movie save failed.";
      }
    }
  }

  /* MEMBERS edit */
  if ($action === 'member_delete') {
    $id = (int)($_POST['membersid'] ?? 0);
    if ($id > 0) {
      $statement = $dbconnect->prepare("DELETE FROM members WHERE membersid=?");
      $statement->bind_param("i", $id);
      $statement->execute();
      $statement->close();
      $member_msg = "Member deleted.";
    }
  }

  if ($action === 'member_add' || $action === 'member_update') {
    $id       = (int)($_POST['membersid'] ?? 0);
    $username = trim_val('username', $_POST);
    $email    = trim_val('email', $_POST);
    $pass_in  = trim_val('password', $_POST);

    if ($username === '') {
      $member_msg = "Username is required.";
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $member_msg = "Invalid email address.";
    } else {
      if ($action === 'member_add') {
        if ($pass_in === '') {
          $member_msg = "Password is required for new user.";
        } else {
          $hash = password_hash($pass_in, PASSWORD_DEFAULT);
          $statement = $dbconnect->prepare("INSERT INTO members (username, password, email) VALUES (?,?,?)");
          $email_param = ($email !== '' ? $email : null);
          $statement->bind_param("sss", $username, $hash, $email_param);
          $ok = $statement->execute();
          if (!$ok && $dbconnect->errno === 1062) {
            $member_msg = "Username already exists.";
          } elseif (!$ok) {
            $member_msg = "Could not add user.";
          } else {
            $member_msg = "User added.";
          }
          $statement->close();
        }
      } else {
        if ($id > 0) {
          if ($pass_in !== '') {
            $hash = password_hash($pass_in, PASSWORD_DEFAULT);
            $statement = $dbconnect->prepare("UPDATE members SET username=?, email=?, password=? WHERE membersid=?");
            $email_param = ($email !== '' ? $email : null);
            $statement->bind_param("sssi", $username, $email_param, $hash, $id);
          } else {
            $statement = $dbconnect->prepare("UPDATE members SET username=?, email=? WHERE membersid=?");
            $email_param = ($email !== '' ? $email : null);
            $statement->bind_param("ssi", $username, $email_param, $id);
          }
          $ok = $statement->execute();
          if (!$ok && $dbconnect->errno === 1062) {
            $member_msg = "Username already exists.";
          } elseif (!$ok) {
            $member_msg = "Could not update user.";
          } else {
            $member_msg = "User updated.";
          }
          $statement->close();
        }
      }
    }
  }

  /*  SHOWTIMES edit for seat and tickets*/
  if ($action === 'showtime_delete') {
    $id = (int)($_POST['showtimeid'] ?? 0);
    if ($id > 0) {
      $stmt = $dbconnect->prepare("DELETE FROM showtimes WHERE showtimeid=?");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $stmt->close();
      $showtime_msg = "Showtime deleted.";
    }
  }

  if ($action === 'showtime_add' || $action === 'showtime_update') {
    $showtimeid = (int)($_POST['showtimeid'] ?? 0);
    $movie_id   = (int)($_POST['movie_id'] ?? 0);
    $hall_id    = (int)($_POST['hall_id'] ?? 0);
    $show_date  = trim_val('show_date', $_POST); // trim to YYYY-MM-DD
    $show_time  = trim_val('show_time', $_POST); // trim to HH:MM

    if ($movie_id <= 0 || $hall_id <= 0 || $show_date === '' || $show_time === '') {
      $showtime_msg = "All fields are required.";
    } else {
      if ($action === 'showtime_add') {
        $stmt = $dbconnect->prepare("
          INSERT INTO showtimes (movie_id, show_date, show_time, hall_id)
          VALUES (?,?,?,?)
        ");
        $stmt->bind_param("issi", $movie_id, $show_date, $show_time, $hall_id);
        $ok = $stmt->execute();
        $new_showtime_id = $stmt->insert_id;
        $stmt->close();

        if ($ok && $new_showtime_id) {
          // insert showtime seats
          $seat_stmt = $dbconnect->prepare("SELECT seatsid FROM seats WHERE hall_id=?");
          $seat_stmt->bind_param("i", $hall_id);
          $seat_stmt->execute();
          $seat_result = $seat_stmt->get_result();
          while ($seat = $seat_result->fetch_assoc()) {
            $seat_id = (int)$seat['seatsid'];
            $ins = $dbconnect->prepare("
              INSERT INTO showtime_seats (showtime_id, seat_id, is_booked)
              VALUES (?,?,0)
            ");
            $ins->bind_param("ii", $new_showtime_id, $seat_id);
            $ins->execute();
            $ins->close();
          }
          $seat_stmt->close();
          $showtime_msg = "Showtime added.";
        } else {
          $showtime_msg = "Could not add showtime.";
        }
      } else {
        $stmt = $dbconnect->prepare("
          UPDATE showtimes
          SET movie_id=?, show_date=?, show_time=?, hall_id=?
          WHERE showtimeid=?
        ");
        $stmt->bind_param("issii", $movie_id, $show_date, $show_time, $hall_id, $showtimeid);
        $ok = $stmt->execute();
        $stmt->close();
        $showtime_msg = $ok ? "Showtime updated." : "Could not update showtime.";
        // Do not generate completely new showtime_seats to avoid wiping existing bookings
      }
    }
  }
}

/* GET handlers*/

/* edit movie,GET */
if (isset($_GET['edit_movie'])) {
  $id = (int)$_GET['edit_movie'];
  if ($id > 0) {
    $statement = $dbconnect->prepare("SELECT * FROM movies WHERE moviesid=?");
    $statement->bind_param("i", $id);
    $statement->execute();
    $movie_editing = $statement->get_result()->fetch_assoc() ?: null;
    $statement->close();
  }
}

/* edit member,GET */
if (isset($_GET['edit_member'])) {
  $id = (int)$_GET['edit_member'];
  if ($id > 0) {
    $statement = $dbconnect->prepare("SELECT * FROM members WHERE membersid=?");
    $statement->bind_param("i", $id);
    $statement->execute();
    $member_editing = $statement->get_result()->fetch_assoc() ?: null;
    $statement->close();
  }
}

/* edit showtime,GET */
if (isset($_GET['edit_showtime'])) {
  $id = (int)$_GET['edit_showtime'];
  if ($id > 0) {
    $stmt = $dbconnect->prepare("SELECT * FROM showtimes WHERE showtimeid=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $showtime_editing = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
  }
}

/* Lists for all*/
/* List movies */
$movie_list = [];
if ($res = $dbconnect->query("SELECT moviesid, movie_name, release_date, genre, runtime, rating FROM movies ORDER BY moviesid ASC")) {
  $movie_list = fetch_all_rows($res);
  $res->free();
}

/* List members */
$member_list = [];
if ($res = $dbconnect->query("SELECT membersid, username, email FROM members ORDER BY membersid ASC")) {
  $member_list = fetch_all_rows($res);
  $res->free();
}

/* fetch movies for show_time dropdown */
$all_movies = [];
if ($res = $dbconnect->query("SELECT moviesid, movie_name FROM movies ORDER BY movie_name ASC")) {
  $all_movies = fetch_all_rows($res);
  $res->free();
}

/* fetch halls for show_time dropdown */
$all_halls = [];
if ($res = $dbconnect->query("SELECT hallsid, hall_name FROM halls ORDER BY hallsid ASC")) {
  $all_halls = fetch_all_rows($res);
  $res->free();
}

/* list show_times */
$showtime_list = [];
$q = "
  SELECT s.showtimeid, s.show_date, s.show_time,
         m.movie_name,
         h.hall_name
  FROM showtimes s
  JOIN movies m ON s.movie_id = m.moviesid
  JOIN halls h ON s.hall_id = h.hallsid
  ORDER BY s.show_date, s.show_time
";
if ($res = $dbconnect->query($q)) {
  $showtime_list = fetch_all_rows($res);
  $res->free();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Control</title>
  <link rel="stylesheet" href="style.css">
</head>
<?php require __DIR__ . '/header.php'; ?>
<body>
<main>
  <div class="admin-body">
  <h1>Admin</h1>
  <p>Logged in as <strong><?= htmlspecialchars($user) ?></strong></p>

  <!--movies section-->
  <section>
    <h2>Movies</h2>

    <?php if ($movie_msg): ?>
      <p class="notice"><?= htmlspecialchars($movie_msg) ?></p>
    <?php endif; ?>

    <div class="admin-wrap">
      <!-- movie form -->
      <div>
        <h3><?= $movie_editing ? 'Edit Movie #'.(int)$movie_editing['moviesid'] : 'Add Movie' ?></h3>
        <form method="post">
          <?php if ($movie_editing): ?>
            <input type="hidden" name="action" value="movie_update">
            <input type="hidden" name="moviesid" value="<?= (int)$movie_editing['moviesid'] ?>">
          <?php else: ?>
            <input type="hidden" name="action" value="movie_add">
          <?php endif; ?>

          <label>Movie Name
            <input name="movie_name" required value="<?= $movie_editing ? htmlspecialchars($movie_editing['movie_name']) : '' ?>">
          </label>

          <label>Synopsis
            <textarea name="synopsis" rows="4" required><?= $movie_editing ? htmlspecialchars($movie_editing['synopsis']) : '' ?></textarea>
          </label>

          <label>Main Cast (comma-separated)
            <input name="main_cast" value="<?= $movie_editing ? htmlspecialchars($movie_editing['main_cast'] ?? '') : '' ?>">
          </label>

          <div class="admin-row">
            <label>
              Language
              <input name="language" value="<?= $movie_editing ? htmlspecialchars($movie_editing['language'] ?? '') : '' ?>">
            </label>
            <label>
              Genre
              <input name="genre" value="<?= $movie_editing ? htmlspecialchars($movie_editing['genre'] ?? '') : '' ?>">
            </label>
          </div>

          <div class="admin-row">
            <label>
              Release Date
              <input type="date" name="release_date" required value="<?= $movie_editing ? htmlspecialchars($movie_editing['release_date']) : '' ?>">
            </label>
            <label>
              Runtime (e.g., 2 hr 15 mins)
              <input name="runtime" value="<?= $movie_editing ? htmlspecialchars($movie_editing['runtime'] ?? '') : '' ?>">
            </label>
          </div>

          <div class="admin-row">
            <label>
              Rating (e.g., PG-13, NC-16, M-18)
              <input name="rating" value="<?= $movie_editing ? htmlspecialchars($movie_editing['rating'] ?? '') : '' ?>">
            </label>
            <label>
              Poster Path (e.g., media/file.jpg or image url)
              <input name="poster_path" value="<?= $movie_editing ? htmlspecialchars($movie_editing['poster_path'] ?? '') : '' ?>">
            </label>
          </div>

          <button type="submit"><?= $movie_editing ? 'Update Movie' : 'Add Movie' ?></button>
          <?php if ($movie_editing): ?><a class="btn" href="admin.php">Cancel</a><?php endif; ?>
        </form>
      </div>

      <!-- movie list -->
      <div>
        <h3>All Movies</h3>
        <?php if (!$movie_list): ?>
          <p>No movies yet.</p>
        <?php else: ?>
          <table class="movies-table">
            <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Release</th>
              <th>Genre</th>
              <th>Runtime</th>
              <th>Rating</th>
              <th>Actions</th>
            </tr>
            </thead>
            <tbody>
              <?php foreach ($movie_list as $m): ?>
                <tr>
                  <td><?= (int)$m['moviesid'] ?></td>
                  <td><?= htmlspecialchars($m['movie_name']) ?></td>
                  <td><?= htmlspecialchars($m['release_date']) ?></td>
                  <td><?= htmlspecialchars($m['genre']) ?></td>
                  <td><?= htmlspecialchars($m['runtime'] ?? '') ?></td>
                  <td><?= htmlspecialchars($m['rating']) ?></td>
                  <td>
                    <a class="btn edit-btn" href="admin.php?edit_movie=<?= (int)$m['moviesid'] ?>">Edit</a>
                    <form method="post" style="display:inline" onsubmit="return confirm('Delete this movie?');">
                      <input type="hidden" name="action" value="movie_delete">
                      <input type="hidden" name="moviesid" value="<?= (int)$m['moviesid'] ?>">
                      <button type="submit" class="btn delete-btn">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!--members page section-->
  <section style="margin-top:48px;">
    <h2>Members</h2>

    <?php if ($member_msg): ?>
      <p class="notice"><?= htmlspecialchars($member_msg) ?></p>
    <?php endif; ?>

    <div class="admin-wrap">
      <!-- form -->
      <div>
        <h3><?= $member_editing ? 'Edit Member #'.(int)$member_editing['membersid'] : 'Add Member' ?></h3>
        <form method="post">
          <?php if ($member_editing): ?>
            <input type="hidden" name="action" value="member_update">
            <input type="hidden" name="membersid" value="<?= (int)$member_editing['membersid'] ?>">
          <?php else: ?>
            <input type="hidden" name="action" value="member_add">
          <?php endif; ?>

          <label>Username
            <input name="username" required value="<?= $member_editing ? htmlspecialchars($member_editing['username']) : '' ?>">
          </label>

          <label>Email
            <input type="email" name="email" value="<?= $member_editing ? htmlspecialchars($member_editing['email'] ?? '') : '' ?>">
          </label>

          <label>Password <?php if ($member_editing): ?>(leave blank to keep current)<?php endif; ?>
            <input type="password" name="password" <?= $member_editing ? '' : 'required' ?>>
          </label>

          <button type="submit"><?= $member_editing ? 'Update Member' : 'Add Member' ?></button>
          <?php if ($member_editing): ?><a class="btn" href="admin.php">Cancel</a><?php endif; ?>
        </form>
      </div>

      <!-- list-->
      <div>
        <h3>All Members</h3>
        <?php if (!$member_list): ?>
          <p>No members yet.</p>
        <?php else: ?>
          <table>
            <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Email</th>
              <th>Actions</th>
            </tr>
            </thead>
            <tbody>
              <?php foreach ($member_list as $m): ?>
                <tr>
                  <td><?= (int)$m['membersid'] ?></td>
                  <td><?= htmlspecialchars($m['username']) ?></td>
                  <td><?= htmlspecialchars($m['email'] ?? '') ?></td>
                  <td>
                    <a class="btn edit-btn" href="admin.php?edit_member=<?= (int)$m['membersid'] ?>">Edit</a>
                    <form method="post" style="display:inline" onsubmit="return confirm('Delete this member?');">
                      <input type="hidden" name="action" value="member_delete">
                      <input type="hidden" name="membersid" value="<?= (int)$m['membersid'] ?>">
                      <button type="submit" class="btn delete-btn">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!--showtimes section-->
  <section style="margin-top:48px;">
    <h2>Showtimes</h2>

    <?php if ($showtime_msg): ?>
      <p class="notice"><?= htmlspecialchars($showtime_msg) ?></p>
    <?php endif; ?>

    <div class="admin-wrap">
      <!-- form -->
      <div>
        <h3><?= $showtime_editing ? 'Edit Showtime #'.(int)$showtime_editing['showtimeid'] : 'Add Showtime' ?></h3>
        <form method="post">
          <?php if ($showtime_editing): ?>
            <input type="hidden" name="action" value="showtime_update">
            <input type="hidden" name="showtimeid" value="<?= (int)$showtime_editing['showtimeid'] ?>">
          <?php else: ?>
            <input type="hidden" name="action" value="showtime_add">
          <?php endif; ?>

          <label>
            Movie
            <select name="movie_id" required>
              <option value="">-- select movie --</option>
              <?php foreach ($all_movies as $mv): ?>
                <option value="<?= (int)$mv['moviesid'] ?>"
                  <?= $showtime_editing && (int)$showtime_editing['movie_id'] === (int)$mv['moviesid'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($mv['movie_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label>
            Hall
            <select name="hall_id" required>
              <option value="">-- select hall --</option>
              <?php foreach ($all_halls as $hl): ?>
                <option value="<?= (int)$hl['hallsid'] ?>"
                  <?= $showtime_editing && (int)$showtime_editing['hall_id'] === (int)$hl['hallsid'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($hl['hall_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <div class="admin-row">
            <label>
              Show Date
              <input type="date" name="show_date" required
                value="<?= $showtime_editing ? htmlspecialchars($showtime_editing['show_date']) : '' ?>">
            </label>
            <label>
              Show Time
              <input type="time" name="show_time" required
                value="<?= $showtime_editing ? htmlspecialchars(substr($showtime_editing['show_time'],0,5)) : '' ?>">
            </label>
          </div>

          <button type="submit"><?= $showtime_editing ? 'Update Showtime' : 'Add Showtime' ?></button>
          <?php if ($showtime_editing): ?><a class="btn" href="admin.php">Cancel</a><?php endif; ?>
        </form>
      </div>

      <!-- list for show times -->
      <div>
        <h3>Showtimes</h3>
        <?php if (!$showtime_list): ?>
          <p>No showtimes yet.</p>
        <?php else: ?>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Movie</th>
                <th>Hall</th>
                <th>Date</th>
                <th>Time</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($showtime_list as $s): ?>
                <tr>
                  <td><?= (int)$s['showtimeid'] ?></td>
                  <td><?= htmlspecialchars($s['movie_name']) ?></td>
                  <td><?= htmlspecialchars($s['hall_name']) ?></td>
                  <td><?= htmlspecialchars($s['show_date']) ?></td>
                  <td><?= htmlspecialchars(substr($s['show_time'],0,5)) ?></td>
                  <td>
                    <a class="btn edit-btn" href="admin.php?edit_showtime=<?= (int)$s['showtimeid'] ?>">Edit</a>
                    <form method="post" style="display:inline" onsubmit="return confirm('Delete this showtime?');">
                      <input type="hidden" name="action" value="showtime_delete">
                      <input type="hidden" name="showtimeid" value="<?= (int)$s['showtimeid'] ?>">
                      <button type="submit" class="btn delete-btn">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <div class="bottom"><a href="logout.php">Logout</a></div>
  </div>
</main>
<footer>&copy; 2025 Lume</footer>
</body>
</html>



