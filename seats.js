document.addEventListener("DOMContentLoaded", function () {
  var seatButtons = document.querySelectorAll(".seat:not(.unavailable)");
  var selectedSeats = [];
  var pricePerSeat = 12;

  seatButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      var seatId = this.getAttribute("data-seat-id");
      var seatLabel = this.getAttribute("data-seat-label");

      if (this.classList.contains("selected")) {
        this.classList.remove("selected");
        selectedSeats = selectedSeats.filter(s => s.seatId !== seatId);
      } else {
        this.classList.add("selected");
        selectedSeats.push({ seatId: seatId, seatLabel: seatLabel });
      }

      updateSummary();
    });
  });

  function updateSummary() {
    var seatLabels = selectedSeats.map(s => s.seatLabel).join(", ");
    var seatIds = selectedSeats.map(s => s.seatId).join(",");
    var qty = selectedSeats.length;
    var total = qty * pricePerSeat;

    document.getElementById("seats").value = seatLabels;
    document.getElementById("selected_seats").value = seatIds;
    document.getElementById("qty_seats").value = qty;
    document.getElementById("total").value = total.toFixed(2);
  }
});



