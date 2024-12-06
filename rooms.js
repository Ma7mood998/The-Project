// To make sure the script runs only when DOM finished loading
document.addEventListener("DOMContentLoaded", () => {
    // Inizialize variables from (rooms.php)
    const container = document.getElementById("rooms-container");
    const filterForm = document.getElementById("filter-form");
    const availabilitySelect = document.getElementById("filter-availability");
    const capacitySelect = document.getElementById("filter-capacity");
    const searchInput = document.getElementById("search-room");
    const floorSelect = document.getElementById("filter-floor");
    const departmentSelect = document.getElementById("filter-department");

    // Function to fetch rooms based on filters

    // Accepts filter objects with their equivalant values
    function fetchRooms(filters = {}) {
        // Convert them to srting so we can use the GET request
        const params = new URLSearchParams(filters).toString();

        // Display loading message "Doesnt appear because the pc is fast"
        container.innerHTML = "<p>Loading rooms...</p>";

        // Use AJAX to display room info in (rooms.php)

        // Sends a GET request to (fetch_rooms.php), putting the filters as query parameters.
        fetch(`fetch_rooms.php?${params}`)

            // We used "".then" instead of "await" be able to chain multiple actions without converting the function to "async".
            .then(response => response.json())
            .then(data => {

                // Clear previous content
                container.innerHTML = "";
                
                // No rooms found
                if (data.length === 0) {
                    container.innerHTML = "<p>No rooms found based on the selected filters.</p>";
                } else {
                    
                    // Rooms are dynamically added as an "<article>" in (rooms.php)
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
            // Error handling
            .catch(error => {
                console.error("Error fetching rooms:", error);
                container.innerHTML = "<p>Failed to load rooms. Please try again later.</p>";
            });
    }

    // Calls the previous function (fetchRooms) with default filters to show all available rooms
    fetchRooms({ availability: "available", capacity: "all", floor: "all", department: "all" });

    // Handle filter form submission

    // To prevent form from reloading page "Default behaviour of form when submitting"
    filterForm.addEventListener("submit", function (event) {
        event.preventDefault(); 

        // Capture filter values (|| "all" is incase the filter is empty)
        const filters = {
            availability: availabilitySelect.value || "all",
            capacity: capacitySelect.value || "all",
            floor: floorSelect.value || "all",
            department: departmentSelect.value || "all",
            room_name: searchInput.value.trim() || ""
        };

        // Pass the captured filter values to the function "fetchRooms" to fetch filtered room data dynamically.
        fetchRooms(filters);
        
    });

    // Script for the reset button in (rooms.php)
    // It resets all form inputs to their default values and fetches the default set of rooms
    document.getElementById("reset-filters").addEventListener("click", () => {
        filterForm.reset(); // Reset form fields
        fetchRooms({ availability: "available", capacity: "all", floor: "all", department: "all" });
    });
});
