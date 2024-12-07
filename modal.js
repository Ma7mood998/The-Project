document.addEventListener("DOMContentLoaded", () => {
    const popupOverlay = document.getElementById("popupOverlay");
    const popupBody = document.getElementById("popupBody");
    const closePopup = document.getElementById("closePopup");
    
    // Handle "View Details" button click to open the modal and fetch room details
    document.addEventListener("click", async (event) => {
        if (event.target.classList.contains("view-details")) {
            const roomId = event.target.getAttribute("data-room-id");

            // Show loading message in modal
            popupBody.innerHTML = "<p>Loading details...</p>";
            popupOverlay.classList.add("active"); // Show modal overlay

            try {
                // Fetch room details from room_details.php
                const response = await fetch(`room_details.php?room_id=${roomId}`);
                if (!response.ok) {
                    throw new Error("Failed to load room details.");
                }

                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }

                // Populate the modal with room details
                popupBody.innerHTML = `
                    <h2>${data.room_name}</h2>
                    <p>Capacity: ${data.capacity}</p>
                    <p>Equipment: ${data.equipment}</p>
                    <p>Floor: ${data.floor}</p>
                    <p>Department: ${data.department}</p>
                    
                    <h3>Available Schedules</h3>
                    <form id="booking-form">
                        ${generateScheduleHTML(data.schedule)}
                        <button type="submit">Book Now</button>
                    </form>
                `;

                // Handle booking submission
                const bookingForm = document.getElementById("booking-form");
                bookingForm.addEventListener("submit", async function (e) {
                    e.preventDefault(); // Prevent page reload

                    const selectedSchedule = bookingForm.querySelector('input[name="schedule_id"]:checked');
                    if (!selectedSchedule) {
                        alert("Please select a schedule.");
                        return;
                    }

                    const scheduleId = selectedSchedule.value;

                    // Submit booking request
                    try {
                        const bookingResponse = await fetch('room_details.php', {
                            method: 'POST',
                            body: new URLSearchParams({
                                schedule_id: scheduleId
                            }),
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        });

                        const bookingData = await bookingResponse.json();
                        if (bookingData.success) {
                            alert(bookingData.success);
                            popupOverlay.classList.remove("active");
                        } else {
                            alert(bookingData.error || 'An error occurred during booking.');
                        }
                    } catch (error) {
                        console.error(error);
                        alert('Error while booking the room.');
                    }
                });

            } catch (error) {
                console.error(error);
                popupBody.innerHTML = `<p>Error: ${error.message}</p>`;
            }
        }
    });

    // Close the modal
    closePopup.addEventListener("click", () => {
        popupOverlay.classList.remove("active");
        popupBody.innerHTML = ""; // Clear content
    });

    // Close the modal if the overlay is clicked
    popupOverlay.addEventListener("click", (event) => {
        if (event.target === popupOverlay) {
            popupOverlay.classList.remove("active");
            popupBody.innerHTML = ""; // Clear content
        }
    });
});

// Helper function to generate schedule HTML
function generateScheduleHTML(schedule) {
    if (schedule.length === 0) {
        return "<p>No available schedules.</p>";
    }

    return schedule.map(slot => {
        if (slot.status === 'available') {
            return `
                <div>
                    <input type="radio" id="schedule-${slot.schedule_id}" name="schedule_id" value="${slot.schedule_id}" required>
                    <label for="schedule-${slot.schedule_id}">
                        ${slot.available_from} to ${slot.available_to}
                    </label>
                </div>
            `;
        }
        return '';
    }).join('');
}
