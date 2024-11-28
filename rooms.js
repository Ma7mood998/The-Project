// JavaScript code for the room browsing page (rooms.php)


// event listener to select "rooms-container" to add room cards
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("rooms-container");

    // Use (fetch_rooms.php) to fetch ONLY available rooms
    fetch("fetch_rooms.php")
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                container.innerHTML = "<p>No rooms available.</p>";
            } else {
                data.forEach(room => {
                    const card = document.createElement("article");
                    card.className = "room-card";

                    // Room card HTML
                    card.innerHTML = `
                        <h2>${room.room_name}</h2>
                        <p>Capacity: ${room.capacity}</p>
                        <p>Equipment: ${room.equipment}</p>
                        <a href="rooms+.php?room_id=${room.room_id}" class="button">View Details</a>
                    `;

                    container.appendChild(card);
                });
            }
        })
        .catch(error => {
            console.error("Error fetching rooms:", error);
            container.innerHTML = "<p>Failed to load rooms. Please try again later.</p>";
        });
});