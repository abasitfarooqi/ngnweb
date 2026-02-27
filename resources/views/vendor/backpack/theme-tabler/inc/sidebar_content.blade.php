@include(backpack_view('inc.menu_items'))

@push('after_scripts')
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof tabler === 'undefined' || !tabler.bootstrap) return;
        [...document.querySelectorAll('aside .dropdown-toggle.active')].forEach(el => {
            let bsDropdown = tabler.bootstrap.Dropdown.getInstance(el);
            if (typeof bsDropdown !== 'undefined' && bsDropdown !== null) {
                bsDropdown.show();
                el.blur();
            }
        });

        [...document.querySelectorAll('aside .nav-item.dropdown')].forEach(el => {
            let bsDropdown = tabler.bootstrap.Dropdown.getInstance(el.firstElementChild);
            if (typeof bsDropdown !== 'undefined' && bsDropdown !== null) {
                bsDropdown._config.autoClose = false;
                bsDropdown.update();
                bsDropdown._element.addEventListener('show.bs.dropdown', function(e) {
                    let openDropdownInstance = document.querySelector('aside .nav-link.dropdown-toggle.show');
                    if (openDropdownInstance !== null) {
                        let openDropdown = tabler.bootstrap.Dropdown.getInstance(openDropdownInstance);
                        if (openDropdown) openDropdown.hide();
                    }
                });
            }
        });

        [...document.querySelectorAll('header.top .dropdown-toggle, aside .dropdown-toggle')].forEach(el => {
            let bsDropdown = tabler.bootstrap.Dropdown.getInstance(el);
            if (typeof bsDropdown !== 'undefined' && bsDropdown !== null) {
                if (!bsDropdown._element.classList.contains('nav-link')) {
                    bsDropdown._element.addEventListener('show.bs.dropdown', function(e) {
                        let targetParent = e.target.parentElement.classList.contains('dropend') ? e.target.parentElement.parentElement : e.target.parentElement.parentElement.parentElement;
                        let openDropdownInstances = targetParent.querySelectorAll('.dropdown-toggle.show');
                        if (openDropdownInstances !== null) {
                            openDropdownInstances.forEach(function(openDropdownInstance) {
                                let openDropdown = tabler.bootstrap.Dropdown.getInstance(openDropdownInstance);
                                if (openDropdown) openDropdown.hide();
                            });
                        }
                    });
                } else {
                    bsDropdown._element.addEventListener('show.bs.dropdown', function(e) {
                        if (e.target.parentElement.classList.contains('active') || e.target.classList.contains('active')) {
                            e.target.parentElement.querySelectorAll('.dropdown-toggle.active').forEach(function(openDropdownInstance) {
                                if (openDropdownInstance !== e.target) {
                                    let openDropdown = tabler.bootstrap.Dropdown.getInstance(openDropdownInstance);
                                    if (openDropdown) openDropdown.show();
                                }
                            });
                        }
                    });
                }
            }
        });

        [...document.querySelectorAll('header.top .nav-item.dropdown')].forEach(el => {
            let bsDropdown = tabler.bootstrap.Dropdown.getInstance(el.firstElementChild);
            if (typeof bsDropdown !== 'undefined' && bsDropdown !== null) {
                bsDropdown._config.autoClose = 'outside';
                bsDropdown.update();
            }
        });
    });
</script>
@endpush
