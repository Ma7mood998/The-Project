// To make sure the script runs only when DOM finished loading
document.addEventListener("DOMContentLoaded", async function () {
    // Extract the room_id parameter from the URL using URLSearchParams
    const urlParams = new URLSearchParams(window.location.search);
    const roomId = urlParams.get("room_id");

    // Error message if we dont find a room ID
    if (!roomId) {
        document.getElementById('room-details').innerHTML = '<p>Error: Room ID not provided.</p>';
        return;
    }

    try {
        // Fetch room details using async/await
        const response = await fetch(`room_details.php?room_id=${roomId}`);
        
        // Check if the response is OK
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();

        // Error Handling
        if (data.error) {
            document.getElementById('room-details').innerHTML = `<p>${data.error}</p>`;
            return;
        }

        // Populate room details
        const details = document.getElementById('room-details');
        details.innerHTML = `
            <h2>${data.room_name}</h2>
            <p>Capacity: ${data.capacity}</p>
            <p>Equipment: ${data.equipment}</p>
            <p>Floor: ${data.floor}</p>
            <p>Department: ${data.department}</p>
        `;

        // Populate schedules
        const schedule = document.getElementById('room-schedule');
        if (data.schedule.length === 0) {
            schedule.innerHTML += '<p>No available schedules.</p>';
            return;
        }

        // Schedule HTML using foreach loop to loop through schedules for the room
        let scheduleHTML = '<form id="booking-form">';
        data.schedule.forEach(slot => {
            // Add them dynamically with radio buttons for user selection.
            if (slot.status === 'available') {
                scheduleHTML += `
                    <div>
                        <input type="radio" id="schedule-${slot.schedule_id}" name="schedule_id" value="${slot.schedule_id}" required>
                        <label for="schedule-${slot.schedule_id}">
                            ${slot.available_from} to ${slot.available_to}
                        </label>
                    </div>
                `;
            }
        });

        // Wraps the input elements in a form with a "Book Now" button.
        scheduleHTML += '<button type="submit">Book Now</button></form>';
        schedule.innerHTML += scheduleHTML;

        // Handle booking submission
        const bookingForm = document.getElementById('booking-form');
        bookingForm.addEventListener('submit', async function (e) {
            // Prevent the form from reloading the page when submitting
            e.preventDefault();
        
            const formData = new FormData(bookingForm);
            
            try {
                // Using async/await POST request to send the selected schedule
                const response = await fetch(`room_details.php`, { 
                    method: 'POST', 
                    body: formData 
                });

                // Check if the response is OK
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();

                // Displays a success alert if booking is successful.
                if (data.success) {
                    alert(data.success);
                } else if (data.error) {
                    // Handle error messages returned from (room_details.php)
                    alert(data.error);
                }
            } catch (error) {
                console.error('Error during booking:', error);
                alert('An error occurred while booking.');
            }
        });

    } catch (error) {
        console.error('Error fetching room details:', error);
        document.getElementById('room-details').innerHTML = '<p>Failed to load room details.</p>';
    }
});
