# TODO: Modify Appointment Booking Form

## Tasks
- [ ] Replace "find nearest hospital" button with hospital dropdown in book-appointment.blade.php
- [ ] Add prestation dropdown that loads prestations for selected hospital
- [ ] Display price of selected prestation
- [ ] Update PatientPortalController storeAppointment method to handle hospital_id and prestation_id
- [ ] Add API route to fetch prestations by hospital
- [ ] Update form validation and submission logic
- [ ] Test the form functionality

## Current Status
- Form currently has hardcoded service list and "find nearest hospital" button
- Controller expects service_id but needs to handle prestation_id
- No API routes for dynamic prestation loading
