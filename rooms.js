// JavaScript for Room Browsing Page (rooms.php)

// Select container to add room cards
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("rooms-container");

    // Fetch ONLY available rooms from (fetch_rooms.php)
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
