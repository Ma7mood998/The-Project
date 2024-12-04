document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const roomId = urlParams.get("room_id");

    if (!roomId) {
        document.getElementById('room-details').innerHTML = '<p>Error: Room ID not provided.</p>';
        return;
    }

    // Fetch room details via AJAX
    fetch(`room_details.php?room_id=${roomId}`)
        .then(response => response.json())
        .then(data => {
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

            let scheduleHTML = '<form id="booking-form">';
            data.schedule.forEach(slot => {
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
            scheduleHTML += '<button type="submit">Book Now</button></form>';
            schedule.innerHTML += scheduleHTML;

            // Handle booking submission
const bookingForm = document.getElementById('booking-form');
bookingForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(bookingForm);

    fetch(`room_details.php`, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Display success message
                alert(data.success);

                // Refresh schedules (optional, define fetchRoomDetails if needed)
                // fetchRoomDetails(roomId); 
            } else if (data.error) {
                // Handle error messages returned from the server
                alert(data.error);
            }
        })
        .catch(error => {
            console.error('Error during booking:', error);
            alert('An error occurred while booking.');
        });
});

        })
        .catch(error => {
            console.error('Error fetching room details:', error);
            document.getElementById('room-details').innerHTML = '<p>Failed to load room details.</p>';
        });
});
