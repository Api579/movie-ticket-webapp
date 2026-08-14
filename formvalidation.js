function validateForm(event) {
    // Email
    var email = document.getElementById("email").value.trim();
    var emailPattern = /^[\w.-]+@([\w-]+\.)+[A-Za-z]{2,3}$/;
    
    if (email == "") {
        alert("Email must be filled.");
        document.getElementById("email").focus();
        event.preventDefault();
        return false;
    } 
    
    else if (!emailPattern.test(email)) {
        alert("Invalid Email: please enter a valid email format (user-name@example.com).");
        document.getElementById("email").focus();
        event.preventDefault();
        return false;
    }

    // Username
    var username = document.getElementById("username").value.trim();
    var usernamePattern = /^[A-Za-z0-9_]{3,32}$/;
    if (username == "") {
        alert("Username must be filled.");
        document.getElementById("username").focus();
        event.preventDefault();
        return false;
    } 
    else if (!usernamePattern.test(username)) {
        alert("Invalid Username: Must be 3-32 characters, alphanumeric.");
        document.getElementById("username").focus();
        event.preventDefault();
        return false;
    }

    // Password
    var password = document.getElementById("password").value;
    var passwordConfirm = document.getElementById("passwordConfirm").value;
    
    var passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z0-9]{10,}$/;

    if (password == "") {
        alert("Password must be filled.");
        document.getElementById("password").focus();
        event.preventDefault();
        return false;
    } 

    if (passwordConfirm == "") {
        alert("Please confirm your password.");
        document.getElementById("passwordConfirm").focus();
        event.preventDefault();
        return false;
    }

    if (password !== passwordConfirm) {
        alert("Passwords do not match.");
        document.getElementById("passwordConfirm").focus();
        event.preventDefault();
        return false;
    }

    if (!passwordPattern.test(password)) {
        alert("Invalid Password: must be at least 10 characters with both letters and numbers.");
        document.getElementById("password").focus();
        event.preventDefault();
        return false;
    }

    return true;
}

window.onload = function() {
    var form = document.getElementById("signupForm");
    form.addEventListener("submit", validateForm);
};
