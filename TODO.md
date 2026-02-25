This is a comprehensive roadmap to transform your project into an enterprise-grade application. I have organized these into **logical phases** so you don't break the existing application while upgrading it.

---

### **Phase 1: Infrastructure, Security & Observability**
*Goal: Set up the foundation for roles, mobile access, and performance monitoring.*

- [ ] **Install & Setup Laravel Pulse** (Observability)
    *   `composer require laravel/pulse`
    *   `php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"`
    *   *Task:* Set up the dashboard to monitor slow queries and the `update-visit-statuses` command.
- [ ] **Install Laravel Sanctum** (Mobile API)
    *   `php artisan install:api`
    *   *Task:* Update `User` model with `HasApiTokens`. Create a `LoginController` that returns a Bearer Token for mobile requests.
- [ ] **Implement RBAC (Roles/Permissions)**
    *   `composer require spatie/laravel-permission`
    *   *Task:* Create three roles: `Admin`, `Guide`, `Customer`. Protect routes using `middleware(['role:admin'])`.
- [ ] **Multi-Tenancy Setup**
    *   *Task:* Add an `agency_id` to the `Users` and `Tours` tables. Update global scopes so a Guide only sees tours belonging to their specific Agency.

### **Phase 2: Core Business Logic (Booking & Search)**
*Goal: Make the tour management engine robust and searchable.*

- [ ] **Availability Management (The "Slots" System)**
    *   *Task:* Add a `max_capacity` column to Tours. Create a `Bookings` table.
    *   *Logic:* In the Booking Controller, use a **Database Transaction** to check `if (bookings_count < max_capacity)` before saving a new booking to prevent overbooking.
- [ ] **Recurring Tours Logic**
    *   *Task:* Create a `TourSchedule` model (fields: `day_of_week`, `start_time`).
    *   *Logic:* Create a Console Command that runs weekly to generate "Visit" instances for the next 30 days based on these schedules.
- [ ] **Full-Text & Geospatial Search (Laravel Scout)**
    *   `composer require laravel/scout`
    *   Install Meilisearch (via Docker) or use the Database engine.
    *   *Task:* Add `Searchable` trait to the `Tour` model. 
    *   *Geospatial:* Add `latitude` and `longitude` to the Sites/Tours table. Use a `scopeWithinDistance` query to filter tours near the user.

### **Phase 3: Financials & Document Generation**
*Goal: Handle money and physical check-ins.*

- [ ] **Dynamic Pricing Engine**
    *   *Logic:* Create a `PriceCalculator` service. Add "Season" or "Weekend" multipliers (e.g., `price * 1.2` if the visit date is a Saturday).
- [ ] **Invoicing & QR Codes**
    *   `composer require barryvdh/laravel-dompdf`
    *   `composer require simplesoftwareio/simple-qrcode`
    *   *Task:* Create a Blade view for the ticket. Include a QR code containing the `booking_id`. 
    *   *Logic:* Generate the PDF and email it to the user upon successful booking.

### **Phase 4: Real-Time & Maps**
*Goal: Enhance the UI with live tracking and interactive maps.*

- [ ] **Guide Tracking & Map Panel**
    *   *Frontend:* Install `Leaflet.js` (free alternative to Google Maps).
    *   *Task:* Create an Admin dashboard with a map. 
    *   *Logic:* Create a simple API endpoint `/api/update-location` where a Guide’s mobile phone sends coordinates every 60 seconds. Display these coordinates as moving markers on the Admin map.
- [ ] **Weather Integration (The "Demo" Page)**
    *   *Task:* Create a `WeatherService` that calls OpenWeatherMap API.
    *   *Demo:* On the Tour Detail page, show a "Forecast" badge. If the API returns "Rain," show a warning: *"This tour may be rescheduled due to weather."*

### **Phase 5: Performance Optimization**
*Goal: Ensure the app stays fast under load.*

- [ ] **Redis Caching**
    *   *Task:* In the `.env`, set `CACHE_STORE=redis`.
    *   *Implementation:* Wrap the "Heavy" queries (like the homepage tour list) in a cache remember function:
        ```php
        Cache::remember('active_tours', 3600, function () {
            return Tour::where('active', true)->get();
        });
        ```

---

### **Recommended Order of Operations**
1.  **Observability & RBAC** (So you can see what's happening and secure the app).
2.  **Availability & Bookings** (The heart of the complexity).
3.  **Invoicing & QR** (Provides immediate value).
4.  **Scout & Maps** (The "Polish" phase).











-------------------------------------------
Quando guest schiaccia su una visita e poi accede come admin non appare toast per avvisare che non può