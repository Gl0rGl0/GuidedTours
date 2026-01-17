<div 
    x-data="{ 
        query: '', 
        items: [
            { id: 'home', title: 'Go to Home', icon: 'bi-house', url: '{{ route('home') }}' },
            { id: 'dash', title: 'My Dashboard', icon: 'bi-speedometer2', url: '{{ route('user.dashboard') }}' },
            { id: 'profile', title: 'My Profile', icon: 'bi-person', url: '{{ route('profile') }}' },
            @role('configurator')
            { id: 'admin', title: 'Admin Configurator', icon: 'bi-gear', url: '{{ route('admin.configurator') }}' },
            { id: 'add_place', title: 'Add New Place', icon: 'bi-geo-alt', url: '{{ route('admin.places.create') }}' },
            @endrole
        ],
        get filteredItems() {
            if (this.query === '') return this.items;
            return this.items.filter(item => item.title.toLowerCase().includes(this.query.toLowerCase()));
        },
        select(url) {
            window.location.href = url;
            this.commandOpen = false; // Access global state
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
                    <input x-model="query" type="text" class="form-control border-0 shadow-none bg-transparent" placeholder="What do you need?" autofocus>
                </div>
            </div>
            
            <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                <template x-for="item in filteredItems" :key="item.id">
                    <button 
                        @click="select(item.url)"
                        class="list-group-item list-group-item-action d-flex align-items-center px-4 py-3 border-0"
                    >
                        <i :class="'bi ' + item.icon + ' me-3 text-secondary fs-5'"></i>
                        <span x-text="item.title" class="fw-medium"></span>
                    </button>
                </template>
                <div x-show="filteredItems.length === 0" class="p-4 text-center text-muted">
                    No results found.
                </div>
            </div>
            
            <div class="p-2 bg-light border-top border-secondary-subtle small text-end text-muted">
                <span class="me-2">Select <i class="bi bi-arrow-return-left"></i></span>
                <span style="cursor: pointer" @click="commandOpen = false">Close <i class="bi bi-escape"></i></span>
            </div>
        </div>
    </div>
</div>
