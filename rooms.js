// JavaScript code for the room browsing page (rooms.php)

// Event listener to select "rooms-container" and add room cards
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("rooms-container");
    const filterForm = document.getElementById("filter-form");
    const availabilitySelect = document.getElementById("filter-availability");
    const capacitySelect = document.getElementById("filter-capacity");

    // Function to fetch rooms based on filters
    function fetchRooms(filters = {}) {
        // Build query string from filters
        const params = new URLSearchParams(filters).toString();
        
        // Fetch data from fetch_rooms.php with applied filters
        fetch(`fetch_rooms.php?${params}`)
            .then(response => response.json())
            .then(data => {
                container.innerHTML = "";  // Clear previous content
                if (data.length === 0) {
                    container.innerHTML = "<p>No rooms found based on the selected filters.</p>";
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
    }

    // Fetch rooms by default (showing all available rooms)
    fetchRooms({ availability: "available", capacity: "all" });

    // Handle filter form submission
    filterForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent form from reloading page

        // Get filter values
        const filters = {
            availability: availabilitySelect.value,
            capacity: capacitySelect.value
        };

        // Fetch filtered rooms
        fetchRooms(filters);
    });
});
