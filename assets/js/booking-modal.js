// Booking Modal JavaScript Functionality
// Global variables for booking modal
let bookingModal;
let durationHours;
let currentRoomRates = [];

// Initialize booking modal when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if booking modal exists
    bookingModal = document.getElementById('bookingModal');
    if (bookingModal) {
        initBookingModal();
    }
});

// Initialize booking modal functionality
function initBookingModal() {
    durationHours = document.getElementById('duration_hours');
    
    // Modal show event
    bookingModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const roomType = button ? button.getAttribute('data-room-type') : null;
        const roomTypeId = button ? button.getAttribute('data-room-type-id') : null;
        
        // Update room type display
        const selectedRoomTypeElement = document.getElementById('selectedRoomType');
        if (selectedRoomTypeElement) {
            selectedRoomTypeElement.textContent = roomType || 'Please select a room';
        }
        
        // Set room type ID
        const roomTypeIdInput = document.getElementById('room_type_id');
        if (roomTypeIdInput && roomTypeId) {
            roomTypeIdInput.value = roomTypeId;
        }
        
        // Clear form
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            bookingForm.reset();
            if (roomTypeIdInput && roomTypeId) {
                roomTypeIdInput.value = roomTypeId;
            }
        }
        
        // Set minimum check-in date to current date/time (1 hour from now)
        const now = new Date();
        now.setHours(now.getHours() + 1);
        const minDateTime = now.toISOString().slice(0, 16);
        const checkInInput = document.getElementById('check_in_datetime');
        if (checkInInput) {
            checkInInput.min = minDateTime;
        }
        
        // Load duration options for the selected room type
        if (roomTypeId && roomTypeId !== '0') {
            populateDurationOptions(roomTypeId);
        } else {
            // Clear duration options for invalid room type
            if (durationHours) {
                durationHours.innerHTML = '<option value="">Select Duration</option>';
            }
            const availableDurationsDiv = document.getElementById('availableDurations');
            if (availableDurationsDiv) {
                availableDurationsDiv.innerHTML = '<small class="text-muted">Please select a specific room type to see available durations</small>';
            }
        }
    });
    
    // Add event listener for room type "Book Now" buttons
     const bookNowButtons = document.querySelectorAll('[data-bs-target="#bookingModal"]');
     bookNowButtons.forEach(button => {
         button.addEventListener('click', function() {
             const roomType = this.getAttribute('data-room-type');
             const roomTypeId = this.getAttribute('data-room-type-id');
             
             // Update room type display
             const selectedRoomTypeElement = document.getElementById('selectedRoomType');
             if (selectedRoomTypeElement) {
                 selectedRoomTypeElement.textContent = roomType || 'Please select a room';
             }
             
             // Set room type ID
             const roomTypeIdInput = document.getElementById('room_type_id');
             if (roomTypeIdInput && roomTypeId) {
                 roomTypeIdInput.value = roomTypeId;
             }
             
             // Clear form
             clearBookingForm();
             
             // Set minimum check-in date
             setMinimumCheckInDate();
             
             // Set room type ID again (in case it was cleared)
             if (roomTypeIdInput && roomTypeId) {
                 roomTypeIdInput.value = roomTypeId;
             }
             
             // Load duration options for the selected room type
             if (roomTypeId && roomTypeId !== '0') {
                 populateDurationOptions(roomTypeId);
             } else {
                 // Clear duration options for invalid room type
                 if (durationHours) {
                     durationHours.innerHTML = '<option value="">Select Duration</option>';
                 }
                 const availableDurationsDiv = document.getElementById('availableDurations');
                 if (availableDurationsDiv) {
                     availableDurationsDiv.innerHTML = '<small class="text-muted">Please select a specific room type to see available durations</small>';
                 }
             }
         });
     });
    
    // Duration change event
    if (durationHours) {
        durationHours.addEventListener('change', function() {
            updateCheckOutDateTime();
            updatePriceDisplay();
            checkRoomAvailability();
        });
    }
    
    // Check-in date change event
    const checkInInput = document.getElementById('check_in_datetime');
    if (checkInInput) {
        checkInInput.addEventListener('change', function() {
            updateCheckOutDateTime();
            checkRoomAvailability();
        });
    }
    
    // Form submission
    const submitButton = document.getElementById('submitBooking');
    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = document.getElementById('bookingForm');
            if (!form) return;
            
            // Basic form validation
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (field.type === 'file') {
                    if (!field.files || field.files.length === 0) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                } else if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }
            
            // Validate file upload
            const proofOfPayment = document.getElementById('proof_of_payment');
            if (proofOfPayment && proofOfPayment.files.length > 0) {
                const file = proofOfPayment.files[0];
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (!allowedTypes.includes(file.type)) {
                    showNotification('Please upload a valid file (JPG, PNG, or PDF)', 'error');
                    proofOfPayment.classList.add('is-invalid');
                    return;
                }
                
                if (file.size > maxSize) {
                    showNotification('File size must be less than 5MB', 'error');
                    proofOfPayment.classList.add('is-invalid');
                    return;
                }
                
                proofOfPayment.classList.remove('is-invalid');
            }
            
            // Show loading state
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
            submitButton.disabled = true;
            
            // Prepare form data
            const formData = new FormData(form);
            
            // Submit via AJAX
            fetch('includes/process_booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Booking submitted successfully!', 'success');
                    // Close modal
                    const modalInstance = bootstrap.Modal.getInstance(bookingModal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    // Reset form
                    clearBookingForm();
                } else {
                    showNotification(data.message || 'Booking failed. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                // Reset button state
                submitButton.innerHTML = '<i class="fas fa-check me-1"></i>Confirm Booking';
                submitButton.disabled = false;
            });
        });
    }
}

// Populate duration options based on room type
function populateDurationOptions(roomTypeId) {
    if (!durationHours) return;
    
    const availableDurationsDiv = document.getElementById('availableDurations');
    if (availableDurationsDiv) {
        availableDurationsDiv.innerHTML = '<small class="text-muted">Loading available durations...</small>';
    }
    
    fetch(`includes/get_booking_rates.php?room_type_id=${roomTypeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.rates) {
                currentRoomRates = data.rates;
                
                // Clear existing options
                durationHours.innerHTML = '<option value="">Select Duration</option>';
                
                // Add duration options
                data.rates.forEach(rate => {
                    const option = document.createElement('option');
                    option.value = rate.value;
                    option.textContent = `${rate.label} - ₱${parseFloat(rate.price).toFixed(2)}`;
                    option.setAttribute('data-price', rate.price);
                    durationHours.appendChild(option);
                });
                
                // Update available durations display
                const durationsText = data.rates.map(rate => `${rate.value}h`).join(', ');
                if (availableDurationsDiv) {
                    availableDurationsDiv.innerHTML = `<small class="text-muted">Available durations: ${durationsText}</small>`;
                }
            } else {
                if (availableDurationsDiv) {
                    availableDurationsDiv.innerHTML = '<small class="text-danger">No rates available for this room type</small>';
                }
                durationHours.innerHTML = '<option value="">No durations available</option>';
            }
        })
        .catch(error => {
            console.error('Error loading duration options:', error);
            if (availableDurationsDiv) {
                availableDurationsDiv.innerHTML = '<small class="text-danger">Error loading durations</small>';
            }
            durationHours.innerHTML = '<option value="">Error loading options</option>';
        });
}

// Update check-out date/time based on check-in and duration
function updateCheckOutDateTime() {
    const checkInInput = document.getElementById('check_in_datetime');
    const checkOutInput = document.getElementById('check_out_datetime');
    
    if (!checkInInput || !checkOutInput || !durationHours) return;
    
    const checkInValue = checkInInput.value;
    const durationValue = parseInt(durationHours.value);
    
    if (checkInValue && durationValue) {
        const checkInDate = new Date(checkInValue);
        const checkOutDate = new Date(checkInDate.getTime() + (durationValue * 60 * 60 * 1000));
        
        // Format for datetime-local input (local time, not UTC)
        const year = checkOutDate.getFullYear();
        const month = String(checkOutDate.getMonth() + 1).padStart(2, '0');
        const day = String(checkOutDate.getDate()).padStart(2, '0');
        const hours = String(checkOutDate.getHours()).padStart(2, '0');
        const minutes = String(checkOutDate.getMinutes()).padStart(2, '0');
        const checkOutFormatted = `${year}-${month}-${day}T${hours}:${minutes}`;
        checkOutInput.value = checkOutFormatted;
    } else {
        checkOutInput.value = '';
    }
}

// Update price display based on selected duration
function updatePriceDisplay() {
    const priceDisplay = document.getElementById('total_price_display');
    if (!priceDisplay || !durationHours) return;
    
    const selectedOption = durationHours.options[durationHours.selectedIndex];
    if (selectedOption && selectedOption.value) {
        const price = selectedOption.getAttribute('data-price');
        if (price) {
            priceDisplay.value = parseFloat(price).toFixed(2);
        } else {
            priceDisplay.value = '';
        }
    } else {
        priceDisplay.value = '';
    }
}

// Handle room selection from room selection modal
function handleRoomSelection(roomType, roomTypeId) {
    // Populate booking modal with selected room data
    const selectedRoomTypeElement = document.getElementById('selectedRoomType');
    const roomTypeIdInput = document.getElementById('room_type_id');
    
    if (selectedRoomTypeElement) {
        selectedRoomTypeElement.textContent = roomType;
    }
    if (roomTypeIdInput) {
        roomTypeIdInput.value = roomTypeId;
    }
    
    // Set minimum check-in date to current date/time (1 hour from now)
    const now = new Date();
    now.setHours(now.getHours() + 1);
    const minDateTime = now.toISOString().slice(0, 16);
    const checkInInput = document.getElementById('check_in_datetime');
    if (checkInInput) checkInInput.min = minDateTime;
    
    // Clear and reset form
    const bookingFormElement = document.getElementById('bookingForm');
    if (bookingFormElement) bookingFormElement.reset();
    if (roomTypeIdInput) roomTypeIdInput.value = roomTypeId;
    
    // Load duration options for this room type
    populateDurationOptions(roomTypeId);
    
    // Update price display
    updatePriceDisplay();
    
    // Show booking modal
    const bookingModalInstance = new bootstrap.Modal(bookingModal);
    bookingModalInstance.show();
}

// Show notification function (if not already defined in main script)
if (typeof showNotification === 'undefined') {
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
        notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
}

// Check room availability function
function checkRoomAvailability() {
    const roomTypeId = document.getElementById('room_type_id')?.value;
    const checkInDateTime = document.getElementById('check_in_datetime')?.value;
    const checkOutDateTime = document.getElementById('check_out_datetime')?.value;
    const roomSelect = document.getElementById('room_id');
    const roomAvailabilityText = document.getElementById('roomAvailabilityText');
    
    // Clear room selection
    if (roomSelect) {
        roomSelect.innerHTML = '<option value="">Select a room</option>';
        roomSelect.disabled = true;
    }
    
    // Check if all required fields are filled
    if (!roomTypeId || !checkInDateTime || !checkOutDateTime) {
        if (roomAvailabilityText) {
            roomAvailabilityText.textContent = 'Please select duration and check-in time first';
            roomAvailabilityText.className = 'form-text text-muted';
        }
        return;
    }
    
    // Show loading state
    if (roomAvailabilityText) {
        roomAvailabilityText.textContent = 'Checking room availability...';
        roomAvailabilityText.className = 'form-text text-info';
    }
    
    // Make AJAX request to check availability
    const formData = new FormData();
    formData.append('room_type_id', roomTypeId);
    formData.append('check_in_datetime', checkInDateTime);
    formData.append('check_out_datetime', checkOutDateTime);
    
    fetch('includes/get_available_rooms.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            populateRoomOptions(data.available_rooms);
            if (roomAvailabilityText) {
                if (data.available_rooms.length > 0) {
                    roomAvailabilityText.textContent = `${data.available_rooms.length} room(s) available`;
                    roomAvailabilityText.className = 'form-text text-success';
                } else {
                    roomAvailabilityText.textContent = 'No rooms available for selected time';
                    roomAvailabilityText.className = 'form-text text-danger';
                }
            }
        } else {
            console.error('Error checking room availability:', data.message);
            if (roomAvailabilityText) {
                roomAvailabilityText.textContent = 'Error checking availability';
                roomAvailabilityText.className = 'form-text text-danger';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (roomAvailabilityText) {
            roomAvailabilityText.textContent = 'Error checking availability';
            roomAvailabilityText.className = 'form-text text-danger';
        }
    });
}

// Populate room options in the select dropdown
function populateRoomOptions(availableRooms) {
    const roomSelect = document.getElementById('room_id');
    if (!roomSelect) return;
    
    // Clear existing options
    roomSelect.innerHTML = '<option value="">Select a room</option>';
    
    // Add available rooms
    availableRooms.forEach(room => {
        const option = document.createElement('option');
        option.value = room.id;
        option.textContent = `Room ${room.room_number}`;
        if (room.status === 'occupied') {
            option.textContent += ' (Currently occupied but available for your time)';
        }
        roomSelect.appendChild(option);
    });
    
    // Enable the select if there are rooms available
     roomSelect.disabled = availableRooms.length === 0;
 }

// Clear booking form function
function clearBookingForm() {
    const form = document.getElementById('bookingForm');
    if (form) {
        form.reset();
    }
    
    // Clear room selection
    const roomSelect = document.getElementById('room_id');
    if (roomSelect) {
        roomSelect.innerHTML = '<option value="">Select a room</option>';
        roomSelect.disabled = true;
    }
    
    // Clear room availability text
    const roomAvailabilityText = document.getElementById('roomAvailabilityText');
    if (roomAvailabilityText) {
        roomAvailabilityText.textContent = 'Please select duration and check-in time first';
        roomAvailabilityText.className = 'form-text text-muted';
    }
    
    // Clear duration options
    if (durationHours) {
        durationHours.innerHTML = '<option value="">Select Duration</option>';
    }
    
    // Clear price display
    const priceDisplay = document.getElementById('total_price_display');
    if (priceDisplay) {
        priceDisplay.value = '';
    }
}

// Set minimum check-in date function
function setMinimumCheckInDate() {
    const checkInInput = document.getElementById('check_in_datetime');
    if (checkInInput) {
        const now = new Date();
        now.setHours(now.getHours() + 1);
        const minDateTime = now.toISOString().slice(0, 16);
        checkInInput.min = minDateTime;
    }
 }