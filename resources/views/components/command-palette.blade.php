
<div 
    x-data="{ 
        query: '', 
        selectedIndex: 0,
        items: [
            { id: 'home', title: '{{ __('messages.components.command_palette.items.home') }}', icon: 'bi-house', url: '{{ route('home') }}' },
            @guest
                { id: 'login', title: '{{ __('messages.app.nav.login') }}', icon: 'bi-box-arrow-in-right', url: '{{ route('login') }}' },
                { id: 'register', title: '{{ __('messages.app.nav.register') }}', icon: 'bi-person-plus', url: '{{ route('register') }}' },
            @endguest
            @auth
                @role('Customer')
                    { id: 'dash', title: '{{ __('messages.components.command_palette.items.dashboard') }}', icon: 'bi-speedometer2', url: '{{ route('user.dashboard') }}' },
                @endrole
                @role('Admin')
                    { id: 'admin', title: '{{ __('messages.app.nav.admin') }}', icon: 'bi-gear', url: '{{ route('admin.configurator') }}' },
                    { id: 'planning', title: '{{ __('messages.app.nav.planning') }}', icon: 'bi-calendar-event', url: '{{ route('admin.visit-planning.index') }}' },
                    { id: 'add_place', title: '{{ __('messages.components.command_palette.items.add_place') }}', icon: 'bi-geo-alt', url: '{{ route('admin.places.create') }}' },
                    { id: 'add_visit_type', title: '{{ __('messages.components.command_palette.items.add_visit_type') }}', icon: 'bi-calendar-event', url: '{{ route('admin.visit-types.create') }}' },
                @endrole
                @role('Guide')
                    { id: 'availability', title: '{{ __('messages.app.nav.availability') }}', icon: 'bi-clock-history', url: '{{ route('volunteer.availability.form') }}' },
                    { id: 'my_visits', title: '{{ __('messages.app.nav.my_visits') }}', icon: 'bi-calendar-check', url: '{{ route('volunteer.visits.past') }}' },
                @endrole
                { id: 'profile', title: '{{ __('messages.components.command_palette.items.profile') }}', icon: 'bi-person', url: '{{ route('profile') }}' },
            @endauth
        ],
        get filteredItems() {
            if (this.query === '') return this.items;
            return this.items.filter(item => item.title.toLowerCase().includes(this.query.toLowerCase()));
        },
        focusNext() {
            if (this.selectedIndex < this.filteredItems.length - 1) {
                this.selectedIndex++;
                this.scrollToSelected();
            }
        },
        focusPrev() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
                this.scrollToSelected();
            }
        },
        select(url) {
            window.location.href = url;
            this.commandOpen = false;
        },
        selectCurrent() {
            if (this.filteredItems.length > 0 && this.filteredItems[this.selectedIndex]) {
                this.select(this.filteredItems[this.selectedIndex].url);
            }
        },
        scrollToSelected() {
            this.$nextTick(() => {
                const el = this.$refs['item' + this.selectedIndex];
                if (el) el.scrollIntoView({ block: 'nearest' });
            });
        }
    }"
    x-init="
        $watch('query', value => selectedIndex = 0);
        $watch('commandOpen', value => {
            if(value) {
                setTimeout(() => $refs.searchInput.focus(), 50);
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        })
    "
    @keydown.window="(e) => {
        if (commandOpen) {
            if (e.key === 'ArrowDown') { e.preventDefault(); focusNext(); }
            if (e.key === 'ArrowUp') { e.preventDefault(); focusPrev(); }
            if (e.key === 'Enter') { e.preventDefault(); selectCurrent(); }
        }
    }"
>
    <!-- Modal Backdrop -->
    <div 
        x-show="commandOpen" 
        x-transition.opacity
        class="command-palette-backdrop"
        style="display: none;"
    >
        <div 
            @click.away="commandOpen = false" 
            class="command-palette-modal"
        >
            <div class="p-3 border-bottom border-secondary-subtle">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-primary"></i></span>
                    <input x-ref="searchInput" x-model="query" type="text" class="form-control border-0 shadow-none bg-transparent" placeholder="{{ __('messages.components.command_palette.search_placeholder') }}" autofocus>
                </div>
            </div>
            
            <div class="list-group list-group-flush" style="max-height: 450px; overflow-y: auto;">
                <template x-for="(item, index) in filteredItems" :key="item.id">
                    <button 
                        @click="select(item.url)"
                        @mouseover="selectedIndex = index"
                        :x-ref="'item' + index"
                        :class="{'bg-primary-subtle text-primary': selectedIndex === index}"
                        class="list-group-item list-group-item-action d-flex align-items-center px-4 py-3 border-0"
                    >
                        <i :class="'bi ' + item.icon + ' me-3 fs-5 ' + (selectedIndex === index ? 'text-primary' : 'text-secondary')"></i>
                        <span x-text="item.title" class="fw-medium"></span>
                    </button>
                </template>
                <div x-show="filteredItems.length === 0" class="p-4 text-center text-muted">
                    {{ __('messages.components.command_palette.no_results') }}
                </div>
            </div>
            
            <div class="p-2 bg-light border-top border-secondary-subtle small text-end text-muted">
                <span class="me-2">{{ __('messages.components.command_palette.select') }} <i class="bi bi-arrow-return-left"></i></span>
                <span style="cursor: pointer" @click="commandOpen = false">{{ __('messages.components.command_palette.close') }} <i class="bi bi-escape"></i></span>
            </div>
        </div>
    </div>
</div>
