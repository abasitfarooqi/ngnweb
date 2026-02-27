{{-- Override: load Tabler JS via CDN + local so sidebar/menu work without Basset. --}}
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js" integrity="sha256-tgx2Fg6XYkV027jPEKvmrummSTtgCW/fwV3R3SvZnrk=" crossorigin="anonymous"></script>
<script src="{{ route('backpack.theme-tabler.asset', ['package' => 'theme-tabler', 'path' => 'resources/assets/js/tabler.js']) }}"></script>
