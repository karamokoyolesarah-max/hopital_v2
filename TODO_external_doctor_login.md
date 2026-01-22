# TODO: External Doctor Login Integration

## Tasks
- [ ] Modify `resources/views/medecin/external/register.blade.php` to include login form with tabs
- [ ] Update `ExternalDoctorController` to handle login from registration page
- [ ] Remove separate login route from `routes/web.php`
- [ ] Delete unused login view `resources/views/medecin/external/login.blade.php`
- [ ] Set test passwords for existing external doctors
- [ ] Test login flow with Dr. Jean Kouassi credentials
- [ ] Verify direct redirect to dashboard after login

## Test Credentials
- Dr. Jean Kouassi: dr.kouassi@hospitsis.com / password123
- Dr. Marie Diallo: dr.diallo@hospitsis.com / password123
