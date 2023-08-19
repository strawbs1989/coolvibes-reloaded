function submitRequest() {
    var name = document.getElementById("name").value;
    var songTitle = document.getElementById("selected-song").textContent.split(' - ')[1];
    var artistName = document.getElementById("selected-song").textContent.split(' - ')[2];
    var message = document.getElementById("message").value;

    if (name !== "") {
        var confirmation = google.script.run.doPost({
            name: name,
            songTitle: songTitle,
            artistName: artistName,
            message: message
        });

        document.getElementById("confirmation-message").textContent = confirmation;
        clearForm();
    } else {
        alert("Please enter your name before submitting.");
    }
}
