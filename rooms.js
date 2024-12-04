document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("rooms-container");
    const filterForm = document.getElementById("filter-form");
    const availabilitySelect = document.getElementById("filter-availability");
    const capacitySelect = document.getElementById("filter-capacity");
    const searchInput = document.getElementById("search-room");
    const floorSelect = document.getElementById("filter-floor");
    const departmentSelect = document.getElementById("filter-department");

    // Function to fetch rooms based on filters
    function fetchRooms(filters = {}) {
        const params = new URLSearchParams(filters).toString();

        // Display loading message
        container.innerHTML = "<p>Loading rooms...</p>";

        fetch(`fetch_rooms.php?${params}`)
            .then(response => response.json())
            .then(data => {
                container.innerHTML = ""; // Clear previous content
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
                            <p>Floor: ${room.floor}</p>
                            <p>Department: ${room.department}</p>
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
    fetchRooms({ availability: "available", capacity: "all", floor: "all", department: "all" });

    // Handle filter form submission
    filterForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent form from reloading page

        // Get filter values
        const filters = {
            availability: availabilitySelect.value || "all",
            capacity: capacitySelect.value || "all",
            floor: floorSelect.value || "all",
            department: departmentSelect.value || "all",
            room_name: searchInput.value.trim() || ""
        };

        // Fetch filtered rooms
        fetchRooms(filters);
        
    });

    document.getElementById("reset-filters").addEventListener("click", () => {
        filterForm.reset(); // Reset form fields
        fetchRooms({ availability: "available", capacity: "all", floor: "all", department: "all" }); // Default fetch
    });
});
