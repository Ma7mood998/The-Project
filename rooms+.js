// JavaScript for Room Details Page (rooms+.php)

document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const roomId = urlParams.get("room_id");
    const detailsContainer = document.getElementById("room-details");
    const scheduleContainer = document.getElementById("room-schedule");

    if (!roomId) {
        detailsContainer.innerHTML = "<p>Error: Room ID not provided.</p>";
        return;
    }

    // Fetch room details from (room_details.php)
    fetch(`room_details.php?room_id=${roomId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                detailsContainer.innerHTML = `<p>${data.error}</p>`;
            } else {
                // Insert room details
                detailsContainer.innerHTML = `
                    <h2>${data.room_name}</h2>
                    <p>Capacity: ${data.capacity}</p>
                    <p>Equipment: ${data.equipment}</p>
                `;

                // Insert room schedule
                if (data.schedule.length === 0) {
                    scheduleContainer.innerHTML = "<p>No schedule available.</p>";
                } else {
                    let scheduleHTML = "<h3>Schedule</h3><ul>";
                    data.schedule.forEach(slot => {
                        scheduleHTML += `
                            <li>${slot.available_from} to ${slot.available_to} - ${slot.status}</li>
                        `;
                    });
                    scheduleHTML += "</ul>";
                    scheduleContainer.innerHTML = scheduleHTML;
                }
            }
        })
        .catch(error => {
            console.error("Error fetching room details:", error);
            detailsContainer.innerHTML = "<p>Failed to load room details. Please try again later.</p>";
        });
});