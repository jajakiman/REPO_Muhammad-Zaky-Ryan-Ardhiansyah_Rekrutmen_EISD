/* AksesLoka Map and Wayfinding */
(function () {
    'use strict';

    function calculateDistance(lat1, lon1, lat2, lon2) {
        var R = 6371; // Radius in km
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    window.initAksesLokaMap = function (markersData) {
        var mapContainer = document.getElementById('map');
        if (!mapContainer || typeof L === 'undefined') return;

        // Default Bandung coordinates
        var defaultLat = -6.9175;
        var defaultLng = 107.6191;
        var defaultZoom = 12;

        if (markersData && markersData.length === 1) {
            defaultLat = markersData[0].lat;
            defaultLng = markersData[0].lng;
            defaultZoom = 15;
        }

        var map = L.map('map', {
            center: [defaultLat, defaultLng],
            zoom: defaultZoom,
            scrollWheelZoom: false
        });

        var tileLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> kontributor'
        }).addTo(map);

        tileLayer.on('tileerror', function () {
            var note = document.querySelector('.map-attribution-note');
            if (note && !document.getElementById('tile-error-hint')) {
                var err = document.createElement('span');
                err.id = 'tile-error-hint';
                err.className = 'text-amber-700 font-semibold ml-2';
                err.textContent = '(Tile peta lambat dimuat, gunakan daftar lokasi di bawah)';
                note.appendChild(err);
            }
        });

        var bounds = [];
        var statusColors = {
            accessible: '#065f46',
            partially_accessible: '#92400e',
            inaccessible: '#991b1b',
            not_assessed: '#334155'
        };

        if (markersData && markersData.length > 0) {
            markersData.forEach(function (m) {
                var color = statusColors[m.status] || '#334155';
                var circleMarker = L.circleMarker([m.lat, m.lng], {
                    radius: 9,
                    fillColor: color,
                    color: '#ffffff',
                    weight: 2.5,
                    opacity: 1,
                    fillOpacity: 0.95,
                    title: m.name,
                    alt: m.name + ' - ' + m.status_label
                }).addTo(map);

                var popupContent = '<div class="p-2 space-y-1.5 font-sans">' +
                    '<h4 class="font-bold text-sm text-slate-900 leading-tight m-0">' + escapeHtml(m.name) + '</h4>' +
                    '<p class="text-xs text-slate-600 m-0">' + escapeHtml(m.campus_name) + ' &bull; ' + escapeHtml(m.area_name) + '</p>' +
                    '<p class="m-0"><span class="badge badge-' + escapeHtml(m.status) + ' text-[11px] font-bold">' + escapeHtml(m.status_label) + '</span></p>' +
                    '<p class="pt-1.5 m-0"><a class="button button-primary inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-orange-700 hover:bg-orange-800 no-underline shadow-xs" href="' + escapeHtml(m.url) + '">Detail Fasilitas &rarr;</a></p>' +
                    '</div>';

                circleMarker.bindPopup(popupContent);
                bounds.push([m.lat, m.lng]);
            });

            if (bounds.length > 1) {
                map.fitBounds(bounds, { padding: [40, 40] });
            } else if (bounds.length === 1) {
                map.setView(bounds[0], 15);
            }
        }

        // Invalidate map size so tiles always occupy 100% width/height without visual clipping
        setTimeout(function () {
            map.invalidateSize();
        }, 250);

        window.addEventListener('resize', function () {
            map.invalidateSize();
        });

        // GPS Proximity Sorting
        var gpsBtn = document.getElementById('gps-sort-btn');
        var gpsStatus = document.getElementById('gps-status');

        if (gpsBtn && navigator.geolocation) {
            gpsBtn.addEventListener('click', function () {
                gpsStatus.textContent = 'Mencari koordinat lokasi Anda...';
                gpsStatus.className = 'field-hint text-sm text-blue-800 font-medium';

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        var userLat = position.coords.latitude;
                        var userLng = position.coords.longitude;

                        L.circleMarker([userLat, userLng], {
                            radius: 11,
                            fillColor: '#ea580c',
                            color: '#ffffff',
                            weight: 3,
                            fillOpacity: 1,
                            title: 'Lokasi Anda',
                            alt: 'Lokasi Anda saat ini'
                        }).addTo(map).bindPopup('<strong>Lokasi Anda saat ini</strong>').openPopup();

                        map.setView([userLat, userLng], 14);

                        // Sort the textual list items by distance
                        var listContainer = document.getElementById('location-text-list');
                        if (listContainer) {
                            var items = Array.prototype.slice.call(listContainer.querySelectorAll('.location-item'));
                            items.forEach(function (item) {
                                var lat = parseFloat(item.getAttribute('data-lat'));
                                var lng = parseFloat(item.getAttribute('data-lng'));
                                var dist = calculateDistance(userLat, userLng, lat, lng);
                                item.setAttribute('data-distance', dist);
                                var distEl = item.querySelector('.location-distance');
                                if (distEl) {
                                    distEl.textContent = dist < 1 ? Math.round(dist * 1000) + ' m' : dist.toFixed(1) + ' km';
                                    distEl.style.display = 'inline-block';
                                }
                            });

                            items.sort(function (a, b) {
                                return parseFloat(a.getAttribute('data-distance')) - parseFloat(b.getAttribute('data-distance'));
                            });

                            items.forEach(function (item) {
                                listContainer.appendChild(item);
                            });
                        }

                        gpsStatus.textContent = 'Lokasi berhasil diurutkan berdasarkan jarak terdekat dari Anda.';
                        gpsStatus.className = 'field-hint text-sm text-emerald-800 font-bold';
                    },
                    function () {
                        gpsStatus.textContent = 'Izin lokasi tidak diberikan. Anda tetap dapat menggunakan filter pencarian kampus di atas.';
                        gpsStatus.className = 'field-hint text-sm text-amber-800 font-medium';
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            });
        }
    };

    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
    }
})();
