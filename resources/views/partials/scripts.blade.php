
    <!-- Script Logika Tab & Slider & Detail & Quiz -->
    <script>
        // Pass PHP variable to JS
        const themeActiveColor = "{{ $activeTextClass }}";
        const isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};

        // Logika Tab Switching
        // Global function to switch tabs
        function switchTab(tabName) {
            // Redirect to Filament Admin Panel
            if (tabName === 'admin') {
                window.location.href = '/admin';
                return;
            }

            // Simpan status tab terakhir
            localStorage.setItem('activeTab', tabName);

            // Jika kita sedang di halaman detail/quiz, jangan ganti tab tapi tutup dulu (opsional, tapi lebih baik langsung reset)
            document.getElementById('view-course-detail').classList.add('hidden');

            // Jika sedang di quiz, mungkin perlu konfirmasi, tapi untuk switch tab biasa kita biarkan saja tertutup
            // (Best practice: quiz harus disubmit/dibatalkan dulu, tapi disini kita sembunyikan saja)

            // Tampilkan kembali bottom nav jika tersembunyi
            const bottomNav = document.getElementById('bottom-nav');
            if (bottomNav) {
                 bottomNav.classList.remove('translate-y-full'); // Reset posisi
            }
            
            // Tutup juga view quiz jika terbuka
            const quizView = document.getElementById('view-quiz');
            if (quizView && !quizView.classList.contains('hidden')) {
                quizView.classList.add('hidden');
                // Kita asumsi view reguler akan dibukakan oleh logic tab nanti
            }
            
            // Header reset position
            const headerMain = document.getElementById('header-main');
            if (headerMain) {
                headerMain.classList.remove('-translate-y-full');
            }

            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('fade-in'); // Reset animation
            });

            // Show selected tab
            const target = document.getElementById(tabName.startsWith('view-') ? tabName : 'view-' + tabName);
            if(target) {
                target.classList.remove('hidden');
                setTimeout(() => target.classList.add('fade-in'), 10);
            }

            // Update bottom nav (Active State)
            document.querySelectorAll('.nav-item').forEach(el => {
                const iconActive = el.querySelector('.icon-active');
                const iconInactive = el.querySelector('.icon-inactive');

                el.classList.remove(themeActiveColor); 
                el.classList.add('text-gray-400');

                if (iconActive) iconActive.classList.add('hidden');
                if (iconInactive) iconInactive.classList.remove('hidden');
            });
            
            // Set Active Nav (Bottom Nav)
            const navId = tabName.replace('view-', '');
            const activeNav = document.getElementById('nav-' + navId);
            if (activeNav) {
                activeNav.classList.remove('text-gray-400');
                activeNav.classList.add(themeActiveColor); 
                
                // Add explicit color class if themeActiveColor is empty or dynamic
                if(themeActiveColor.includes('blue')) activeNav.classList.add('text-blue-900');

                const iconActive = activeNav.querySelector('.icon-active');
                const iconInactive = activeNav.querySelector('.icon-inactive');

                if (iconActive) iconActive.classList.remove('hidden');
                if (iconInactive) iconInactive.classList.add('hidden');
            }

            // --- SIDEBAR NAV SYNC (Desktop) ---
            document.querySelectorAll('.sidebar-nav-item').forEach(el => {
                el.classList.remove('bg-blue-50', 'text-blue-900');
                el.classList.add('text-gray-600');
                const sideIconActive = el.querySelector('.sidebar-icon-active');
                const sideIconInactive = el.querySelector('.sidebar-icon-inactive');
                if (sideIconActive) sideIconActive.classList.add('hidden');
                if (sideIconInactive) sideIconInactive.classList.remove('hidden');
            });

            const activeSidebar = document.getElementById('sidebar-nav-' + navId);
            if (activeSidebar) {
                activeSidebar.classList.remove('text-gray-600');
                activeSidebar.classList.add('bg-blue-50', 'text-blue-900');
                const sideIconActive = activeSidebar.querySelector('.sidebar-icon-active');
                const sideIconInactive = activeSidebar.querySelector('.sidebar-icon-inactive');
                if (sideIconActive) sideIconActive.classList.remove('hidden');
                if (sideIconInactive) sideIconInactive.classList.add('hidden');
            }

            // --- UPDATE DESKTOP PAGE TITLE ---
            const pageTitles = {
                'home': 'Beranda',
                'blog': 'Artikel',
                'akademi': 'Akademi',
                'reguler': 'Program Reguler',
                'profil': 'Profil Saya',
                'cart': 'Keranjang',
                'admin': 'Halaman Admin',
                'login': 'Login'
            };
            const titleEl = document.getElementById('desktop-page-title');
            if (titleEl) {
                titleEl.innerText = pageTitles[navId] || navId.charAt(0).toUpperCase() + navId.slice(1);
            }

            // --- RESPONSIVE LAYOUT LOGIC ---
            try {
                if (tabName === 'admin') {
                    // Fetch Enrollments for Admin
                    if(typeof fetchEnrollments === 'function') {
                        fetchEnrollments();
                    }
                } else if (tabName === 'cart') {
                    if(typeof loadCart === 'function') {
                        loadCart();
                    }
                }
            } catch(e) { console.error("Layout resize error", e); }
        }

        // --- GLOBAL CART HELPERS ---
        window.updateCartBadge = async function() {
            if(!isAuthenticated) return;
            try {
                const response = await fetch('/cart/count');
                const data = await response.json();
                const badge = document.getElementById('cart-badge-count');
                if(badge) {
                     if(data.count > 0) {
                        badge.innerText = data.count;
                        badge.classList.remove('hidden');
                        badge.classList.add('inline-flex');
                     } else {
                        badge.classList.add('hidden');
                        badge.classList.remove('inline-flex');
                     }
                }
            } catch(e) { console.error("Cart badge error", e); }
        };

        // Call on load
        if(isAuthenticated) window.updateCartBadge();

        async function getFreshToken() {
            try {
                const res = await fetch('/debug/csrf');
                const data = await res.json();
                return data.token;
            } catch(e) {
                console.error("Failed to refresh token", e);
                // Fallback
                const input = document.querySelector('input[name="_token"]');
                return input ? input.value : document.querySelector('meta[name="csrf-token"]').content;
            }
        }

        async function addToCart(productId) {
            if(!isAuthenticated) {
                alert("Silakan login untuk berbelanja.");
                return;
            }
            
            try {
                const token = await getFreshToken();
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: productId, quantity: 1 })
                });

                const data = await response.json();
                if(response.ok) {
                    alert('Produk masuk keranjang!');
                    window.updateCartBadge();
                } else {
                    alert('Gagal: ' + (data.message || 'Error'));
                }
            } catch(e) {
                console.error(e);
                alert('Gagal menambahkan ke keranjang. Coba refresh halaman.');
            }
        }

        // Initialize Global Courses Data
        let allCourses = [];

        async function fetchCourses() {
            const container = document.getElementById('akademi-container');
            if (!container) return;

            try {
                // Fetch from Web Route (shares session)
                const response = await fetch('/courses');
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const courses = await response.json();
                allCourses = courses; // Store globally

                // Clear Loading
                container.innerHTML = '';

                if (courses.length === 0) {
                    container.innerHTML = '<p class="text-center text-gray-500 py-10">Belum ada program tersedia.</p>';
                    return;
                }

            // Render Cards
                courses.forEach((course, index) => {
                    const card = document.createElement('div');
                    card.className = "bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow cursor-pointer";
                    card.onclick = () => openCourseDetail(index); // Pass index

                    // Icon based on color (Generic logic)
                    let iconBg = `bg-${course.color}-50`;
                    let iconText = `text-${course.color}-600`;
                    let border = `border-${course.color}-100`;
                    
                    // Safe formatting
                    const isFree = course.price === null || course.price === 0;
                    const priceFormatted = isFree ? 'GRATIS' : new Intl.NumberFormat('id-ID', { style: 'currency', currency: course.currency }).format(course.price);

                    card.innerHTML = `
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 ${iconBg} ${iconText}">
                                    <span class="text-2xl">🎓</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded ${iconBg} ${iconText} ${border} uppercase tracking-wide border">${course.type}</span>
                                    <span class="text-xs font-bold text-orange-600">${priceFormatted}</span>
                                </div>
                                <h4 class="font-bold text-gray-900 mt-2 mb-1">${course.title}</h4>
                                <p class="text-[10px] text-gray-500 line-clamp-2">${course.short_desc}</p>
                            </div>
                        </div>
                    `;
                    container.appendChild(card);
                });

                // Update Bill Section
                renderBills(courses);
            } catch (error) {
                console.error("Error fetching courses:", error);
                container.innerHTML = '<p class="text-center text-red-500 py-10">Gagal memuat data. Coba lagi nanti.</p>';
            }
        }

        function renderBills(courses) {
            const billContainer = document.querySelector('.bg-orange-50.rounded-2xl.p-4');
            const pendingCourses = courses.filter(c => c.is_enrolled && c.enrollment_status === 'pending');
            const billSection = document.getElementById('bill-section-list');
            
            if (billSection) {
                if (pendingCourses.length === 0) {
                     billSection.innerHTML = `
                        <div class="text-center py-4 bg-white rounded-xl border border-dashed border-gray-300">
                             <p class="text-sm text-gray-500">Tidak ada tagihan aktif.</p>
                        </div>`;
                } else {
                    billSection.innerHTML = '';
                    pendingCourses.forEach(course => {
                         const price = new Intl.NumberFormat('id-ID', { style: 'currency', currency: course.currency }).format(course.price);
                         const item = document.createElement('div');
                         item.className = "bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center mb-3";
                         item.innerHTML = `
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">${course.title}</h4>
                                <p class="text-xs text-orange-500 font-bold mt-1">${price}</p>
                            </div>
                            <button onclick='openPaymentModal(${JSON.stringify(course)})' class="px-4 py-2 bg-blue-900 text-white text-xs font-bold rounded-lg hover:bg-blue-800">
                                Bayar
                            </button>
                         `;
                         billSection.appendChild(item);
                    });
                }
            }
        }

        // --- QUIZ LOGIC ---
        let activeQuiz = null;
        let quizTimerInterval = null;

        function startQuiz(quizId) {
            if (!isAuthenticated) {
                openAuthModal();
                return;
            }

            // Check previous attempts
            // Convert strings/integers safely
            if ((window.myAttempts || []).map(String).includes(String(quizId))) {
                alert("Anda sudah mengerjakan kuis ini (Satu kali kesempatan).");
                return;
            }

            // Find quiz data
            const quiz = window.quizzesData.find(q => q.id === quizId);
            
            if (!quiz.questions || quiz.questions.length === 0) {
                alert("Kuis ini belum memiliki soal.");
                return;
            }

            activeQuiz = quiz;
            
            // Populate Modal
            document.getElementById('quiz-modal-title').innerText = quiz.title;
            const container = document.getElementById('quiz-questions-container');
            container.innerHTML = '';
            
            // Render Questions
            quiz.questions.forEach((q, index) => {
                const qDiv = document.createElement('div');
                qDiv.className = "mb-6 border-b border-gray-100 pb-6 last:border-0";
                
                let optionsHtml = '';
                if (q.options) {
                    q.options.forEach(opt => {
                        optionsHtml += `
                            <label class="flex items-start space-x-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-all group">
                                <div class="relative flex items-center pt-0.5">
                                    <input type="radio" name="q-${q.id}" value="${opt.id}" class="peer sr-only">
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-blue-600 peer-checked:bg-blue-600 transition-all"></div>
                                </div>
                                <span class="text-sm text-gray-700 group-hover:text-gray-900 select-none flex-1">${opt.text}</span>
                            </label>
                        `;
                    });
                }

                qDiv.innerHTML = `
                    <h5 class="font-bold text-gray-800 mb-3 text-sm flex items-start">
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded mr-2 mt-0.5 whitespace-nowrap">No. ${index + 1}</span>
                        ${q.question_text}
                    </h5>
                    <div class="space-y-2 pl-2">
                        ${optionsHtml}
                    </div>
                `;
                container.appendChild(qDiv);
            });

            // Show Modal
            document.getElementById('quiz-modal').classList.remove('hidden');
            
            // Start Timer
            startTimer((quiz.duration_minutes || 10) * 60);
        }

        function startTimer(durationSeconds) {
            clearInterval(quizTimerInterval);
            let timer = durationSeconds;
            const display = document.getElementById('quiz-timer');
            
            if(display) {
                updateTimerDisplay(timer, display);
                
                quizTimerInterval = setInterval(() => {
                    timer--;
                    updateTimerDisplay(timer, display);
                    
                    if (timer <= 0) {
                        clearInterval(quizTimerInterval);
                        alert("Waktu habis! Jawaban Anda akan dikirim otomatis.");
                        submitQuiz();
                    }
                }, 1000);
            }
        }

        function updateTimerDisplay(seconds, displayElement) {
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            displayElement.innerText = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            if(seconds < 60) displayElement.classList.add('animate-pulse');
        }

        function closeQuizModal() {
            if (confirm("Apakah Anda yakin ingin, progress tidak akan tersimpan?")) {
                document.getElementById('quiz-modal').classList.add('hidden');
                clearInterval(quizTimerInterval);
                activeQuiz = null;
            }
        }

        async function submitQuiz() {
            clearInterval(quizTimerInterval);
            
            // Calculate Score (Client-side) & Collect Answers
            let score = 0;
            let total = activeQuiz.questions.length;
            let correctCount = 0;
            let userAnswers = [];
            
            activeQuiz.questions.forEach(q => {
                const selected = document.querySelector(`input[name="q-${q.id}"]:checked`);
                if (selected) {
                    const selectedOptId = parseInt(selected.value);
                    userAnswers.push({
                        question_id: q.id,
                        option_id: selectedOptId
                    });

                    const correctOpt = q.options.find(opt => opt.is_correct);
                    if (correctOpt && correctOpt.id === selectedOptId) {
                        correctCount++;
                    }
                }
            });
            
            score = Math.round((correctCount / total) * 100);
            
            // UI Feedback
            const btn = document.querySelector('#quiz-modal button[onclick="submitQuiz()"]');
            if(btn) {
                btn.disabled = true;
                btn.innerText = "Mengirim...";
            }

            // Send to Backend
            try {
                const token = document.querySelector('input[name="_token"]').value;
                const response = await fetch('/quiz/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        quiz_id: activeQuiz.id,
                        score: score,
                        answers: userAnswers
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    alert(`Kuis Selesai!\nNilai Akhir Anda: ${score}`);
                    // Update local attempts so they can't retake immediately
                    if(window.myAttempts) window.myAttempts.push(activeQuiz.id);
                } else {
                    alert("Gagal menyimpan nilai: " + (data.message || 'Error'));
                }
            } catch (e) {
                console.error(e);
                alert("Terjadi kesalahan koneksi saat menyimpan nilai. Namun nilai Anda: " + score);
            }

            document.getElementById('quiz-modal').classList.add('hidden');
            activeQuiz = null;
            if(btn) {
                btn.disabled = false;
                btn.innerText = "Kirim Jawaban";
            }
        }

        async function openQuizResult(attemptId) {
            try {
                const response = await fetch(`/quiz/result/${attemptId}`);
                const data = await response.json();

                if (!response.ok) {
                    alert(data.message || 'Gagal memuat hasil.');
                    return;
                }

                const modal = document.getElementById('result-modal');
                const content = document.getElementById('result-content');
                document.getElementById('result-score-display').innerText = `Nilai Akhir: ${data.score}`;
                
                content.innerHTML = ''; // Clear previous

                data.quiz.questions.forEach((q, index) => {
                    const userAnswer = data.answers.find(a => a.quiz_question_id === q.id);
                    const userOptionId = userAnswer ? userAnswer.quiz_option_id : null;

                    let optionsHtml = '';
                    q.options.forEach(opt => {
                        let classes = "p-3 rounded-lg border flex justify-between items-center text-sm ";
                        let icon = "";
                        
                        // Logic Pewarnaan
                        if (opt.is_correct) {
                            classes += "bg-green-50 border-green-200 text-green-800 font-medium ";
                            icon = `@svg('heroicon-s-check-circle', 'w-5 h-5 text-green-600')`;
                        } else if (opt.id === userOptionId && !opt.is_correct) {
                            classes += "bg-red-50 border-red-200 text-red-800 ";
                            icon = `@svg('heroicon-s-x-circle', 'w-5 h-5 text-red-600')`;
                        } else {
                            classes += "bg-white border-gray-200 text-gray-500 ";
                        }

                        // Marker User Selection
                        let userMarker = (opt.id === userOptionId) ? '<span class="text-xs font-bold mr-2">(Jawaban Anda)</span>' : '';

                        optionsHtml += `
                            <div class="${classes} mb-2">
                                <span class="flex-1">${opt.text}</span>
                                <div class="flex items-center">
                                    ${userMarker}
                                    ${icon}
                                </div>
                            </div>
                        `;
                    });

                    // Indikator Jawaban Benar/Salah untuk Judul Soal
                    const isCorrect = userAnswer && q.options.find(o => o.id === userOptionId)?.is_correct;
                    const qStatusIcon = isCorrect 
                        ? `<span class="bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded font-bold ml-2">Benar</span>`
                        : `<span class="bg-red-100 text-red-700 text-[10px] px-2 py-0.5 rounded font-bold ml-2">Salah</span>`;


                    const qDiv = document.createElement('div');
                    qDiv.innerHTML = `
                        <h5 class="font-bold text-gray-800 mb-2 text-sm">
                            <span class="text-gray-500 mr-1">${index + 1}.</span> ${q.question_text}
                            ${qStatusIcon}
                        </h5>
                        <div class="pl-4">
                            ${optionsHtml}
                        </div>
                    `;
                    content.appendChild(qDiv);
                });

                // --- CERTIFICATE LOGIC ---
                const footer = modal.querySelector('.border-t');
                const existingCertBtn = document.getElementById('btn-download-certificate');
                if(existingCertBtn) existingCertBtn.remove(); // Clean up old button

                if (data.quiz.certificate_threshold !== null && data.score >= data.quiz.certificate_threshold) {
                    const certBtn = document.createElement('button');
                    certBtn.id = 'btn-download-certificate';
                    certBtn.className = 'mr-auto bg-green-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-green-700 transition-colors flex items-center shadow-sm';
                    certBtn.innerHTML = `@svg('heroicon-s-academic-cap', 'w-5 h-5 mr-2') Download Sertifikat`;
                    certBtn.onclick = () => {
                        window.open(`/certificate/${attemptId}`, '_blank');
                    };
                    
                    // Insert at beginning of footer (before "Tutup" button)
                    footer.insertBefore(certBtn, footer.firstChild);
                }

                modal.classList.remove('hidden');

            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan koneksi.');
            }
        }

        // --- Logika Halaman Detail ---
        let activeCourse = null;
        let activePlayerTimer = null;

        function closePlayer() {
            const container = document.getElementById('material-player-container');
            const iframe = document.getElementById('player-iframe');
            if(container) container.classList.add('hidden');
            if(iframe) iframe.src = '';
            clearInterval(activePlayerTimer);
        }

        function playMaterial(index) {
            if (!activeCourse || !activeCourse.materials || !activeCourse.materials[index]) return;
            const material = activeCourse.materials[index];
            
            // Determine URL
            let url = material.media_url || (material.type === 'video' ? material.link : null);
            
            if (!url) {
                if (material.type === 'quiz') {
                     alert("Untuk materi Kuis, silakan akses melalui menu Kuis.");
                } else {
                     alert("Materi ini tidak memiliki konten media yang dapat diputar.");
                }
                return;
            }

            // AUTO-CONVERT YOUTUBE LINKS TO EMBED FORMAT
            // Matches: youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID
            const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i;
            const match = url.match(youtubeRegex);
            if (match && match[1]) {
                // Force embed URL with autoplay
                url = `https://www.youtube.com/embed/${match[1]}?autoplay=1&rel=0`;
            }

            const container = document.getElementById('material-player-container');
            const title = document.getElementById('player-title');
            const iframe = document.getElementById('player-iframe');
            const timerContainer = document.getElementById('player-timer');
            const countdown = document.getElementById('timer-countdown');

            if(container && title && iframe) {
                container.classList.remove('hidden');
                title.innerText = material.title;
                iframe.src = url;
                
                // Timer Logic
                clearInterval(activePlayerTimer);
                if (material.timer_seconds > 0 && timerContainer && countdown) {
                    timerContainer.classList.remove('hidden');
                    timerContainer.innerHTML = `
                        @svg('heroicon-s-clock', 'w-3 h-3 mr-1')
                        Wajib tonton: <span id="timer-countdown" class="font-bold text-white mx-0.5">${material.timer_seconds}</span> detik`;
                    
                    let timeLeft = material.timer_seconds;
                    
                    activePlayerTimer = setInterval(() => {
                        timeLeft--;
                        const display = document.getElementById('timer-countdown');
                        if(display) display.innerText = timeLeft;
                        
                        if(timeLeft <= 0) {
                            clearInterval(activePlayerTimer);
                            const timerContainer = document.getElementById('player-timer');
                            if(timerContainer) timerContainer.innerHTML = `<span class="text-green-400 font-bold flex items-center">@svg('heroicon-o-check-circle', 'w-4 h-4 mr-1') Selesai</span>`;
                            
                            // Stop Video
                            const iframe = document.getElementById('player-iframe');
                            if(iframe) {
                                iframe.src = ''; 
                                iframe.classList.add('hidden');
                            }

                            // Show "Time's Up" Message
                            const placeholder = document.getElementById('player-placeholder');
                            if(placeholder) {
                                placeholder.classList.remove('hidden');
                                placeholder.innerHTML = `
                                    <div class="text-center text-white p-6">
                                        @svg('heroicon-o-clock', 'w-16 h-16 mx-auto mb-4 text-white opacity-80')
                                        <h3 class="text-2xl font-bold mb-2">Waktu Habis</h3>
                                        <p class="text-gray-200">Durasi pembelajaran untuk materi ini telah selesai.</p>
                                    </div>
                                `;
                            }
                        }
                    }, 1000);
                } else {
                    if(timerContainer) timerContainer.classList.add('hidden');
                }

                container.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function openCourseDetail(index) {
            const course = allCourses[index];
            if (!course) return;
            activeCourse = course;
            closePlayer(); // Reset player state

            const detailView = document.getElementById('view-course-detail');
            const akademiView = document.getElementById('view-akademi');
            const bottomNav = document.getElementById('bottom-nav');

            // Set Data Dinamis
            document.getElementById('detail-title').innerText = course.title;
            const priceFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: course.currency }).format(course.price);
            document.getElementById('detail-price').innerText = course.price == 0 ? 'GRATIS' : priceFormatted;
            document.getElementById('detail-desc').innerText = course.short_desc; 
            
            // Set Warna Background Icon
            const colorTheme = course.color || 'blue';
            const iconContainer = document.getElementById('detail-bg-color');
            const tagContainer = document.getElementById('detail-tag');

            // Reset classes
            iconContainer.className = `w-full h-40 rounded-2xl flex items-center justify-center mb-4 bg-${colorTheme}-50 text-${colorTheme}-500`;
            tagContainer.className = `inline-block px-3 py-1 rounded-full text-xs font-bold mb-2 bg-${colorTheme}-100 text-${colorTheme}-700`;

            // Render Materials
            const materialContainer = document.querySelector('#view-course-detail .border-t .space-y-2');
            if (materialContainer) {
                materialContainer.innerHTML = '';

                // --- ENROLLMENT LOGIC START ---
                // ... (Enrollment logic handled by renderBill now for home, but this is Detail Page)
                // Let's keep detail page valid, but update button options
                // --- ENROLLMENT LOGIC START ---
                const isEnrolled = course.is_enrolled;
                const enrollStatus = course.enrollment_status;
                const btnEnroll = document.getElementById('btn-enroll-action');

                if (btnEnroll) {
                    btnEnroll.disabled = false;
                    btnEnroll.onclick = null; 
                    
                    if (isEnrolled && enrollStatus === 'active') {
                        btnEnroll.innerText = "Lanjut Belajar (Buka Materi)";
                        btnEnroll.className = "w-full bg-green-600 text-white font-bold py-3.5 rounded-xl hover:bg-green-700 transition-colors shadow-lg shadow-green-900/20 active:scale-[0.98] transform transition-transform";
                        btnEnroll.onclick = () => {
                             const rect = materialContainer.getBoundingClientRect();
                             window.scrollTo({ top: window.scrollY + rect.top - 80, behavior: 'smooth' });
                        };
                    } else if (isEnrolled && enrollStatus === 'pending') {
                         btnEnroll.innerText = "Bayar / Konfirmasi";
                         btnEnroll.className = "w-full bg-orange-500 text-white font-bold py-3.5 rounded-xl hover:bg-orange-600 transition-colors shadow-lg shadow-orange-900/20 active:scale-[0.98] transform transition-transform";
                         btnEnroll.onclick = () => openPaymentModal(course);
                    } else {
                        btnEnroll.innerText = course.price > 0 ? "Daftar Sekarang (Berbayar)" : "Daftar Sekarang (Gratis)";
                        btnEnroll.className = "w-full bg-blue-900 text-white font-bold py-3.5 rounded-xl hover:bg-blue-800 transition-colors shadow-lg shadow-blue-900/20 active:scale-[0.98] transform transition-transform";
                        btnEnroll.onclick = () => handleEnrollClick();
                    }
                }
                // --- ENROLLMENT LOGIC END ---

                if (course.materials && course.materials.length > 0) {
                    course.materials.forEach((material, mIndex) => {
                       const item = document.createElement('div');
                       
                       // Lock Logic
                       const isLocked = (!isEnrolled || enrollStatus !== 'active');
                       const lockClass = isLocked ? "opacity-60 cursor-not-allowed" : "cursor-pointer hover:bg-gray-100 transition-colors active:scale-[0.99]";
                       
                       item.className = `bg-gray-50 p-3 rounded-lg flex items-center justify-between ${lockClass}`;
                       
                       if (!isLocked) {
                           item.onclick = () => playMaterial(mIndex);
                       } else {
                           item.onclick = () => alert("Silakan daftar kursus ini terlebih dahulu untuk mengakses materi.");
                       }
                       
                       // Icon based on type
                       let typeIcon = '';
                       if(material.type === 'video') typeIcon = `@svg('heroicon-s-video-camera', 'w-4 h-4 mr-2 text-blue-500')`;
                       else if(material.type === 'quiz') typeIcon = `@svg('heroicon-s-pencil-square', 'w-4 h-4 mr-2 text-orange-500')`;
                       else typeIcon = `@svg('heroicon-s-document-text', 'w-4 h-4 mr-2 text-gray-500')`;
                       
                       let lockIcon = isLocked ? `@svg('heroicon-s-lock-closed', 'w-3 h-3 text-gray-400 ml-2')` : '';

                       item.innerHTML = `
                            <div class="flex items-center">
                                ${typeIcon}
                                <span class="text-sm font-medium text-gray-700">${mIndex + 1}. ${material.title}</span>
                                ${lockIcon}
                            </div>
                            <span class="text-xs text-gray-400 bg-white px-2 py-1 rounded border">${material.duration || 'Materi'}</span>
                       `;
                       materialContainer.appendChild(item);
                    });
                } else {
                    materialContainer.innerHTML = '<p class="text-gray-500 text-sm italic">Belum ada materi untuk kursus ini.</p>';
                }
            }

            // Sembunyikan View Akademi, Tampilkan Detail
            akademiView.classList.add('hidden');
            detailView.classList.remove('hidden');

            // Sembunyikan Bottom Nav (Efek Full Screen)
            if (bottomNav) {
                bottomNav.classList.add('translate-y-full');
            }

            // Scroll ke atas
            window.scrollTo(0, 0);
        }

        // --- PAYMENT LOGIC ---
        let paymentCourse = null;

        function openPaymentModal(courseOrObject) {
            // Handle if passed as direct object or from click event
            const course = (courseOrObject.id) ? courseOrObject : activeCourse; 
            if (!course) return;

            paymentCourse = course;
            document.getElementById('payment-modal').classList.remove('hidden');
            document.getElementById('payment-course-id').value = course.id;
            
            // Check balance immediately
            if(typeof checkBalanceSufficiency === 'function') {
                checkBalanceSufficiency(course.price);
            }

            // Reset Form
            document.getElementById('payment-form').reset();
            document.getElementById('payment-upload-preview').classList.add('hidden');
            document.getElementById('payment-upload-placeholder').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
            paymentCourse = null;
        }

        function previewPaymentImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('payment-upload-preview').classList.remove('hidden');
                    document.getElementById('payment-upload-preview').querySelector('img').src = e.target.result;
                    document.getElementById('payment-upload-placeholder').classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function redirectToWA(phoneArg) {
             if (!paymentCourse) return;
             // Default Phone if not passed or empty
             const phone = phoneArg || "6281234567890"; 
             const text = encodeURIComponent(`Assalamualaikum Admin, saya ingin konfirmasi pembayaran untuk kursus "${paymentCourse.title}". Mohon info nomor rekening.`);
             window.open(`https://wa.me/${phone}?text=${text}`, '_blank');
        }

        async function handlePaymentSubmit(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-payment');
            btn.disabled = true;
            btn.innerText = "Mengirim...";

            const formData = new FormData(document.getElementById('payment-form'));
            // CSRF not automatically added to FormData in fetch unless X-CSRF-TOKEN header present
            const token = document.querySelector('input[name="_token"]').value;

            try {
                const response = await fetch('/courses/payment', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    alert("Bukti pembayaran berhasil dikirim! Admin akan memverifikasi dalam 1x24 jam.");
                    closePaymentModal();
                    fetchCourses(); // Refresh UI
                } else {
                    alert("Gagal upload: " + (data.message || 'Terjadi kesalahan server'));
                }
            } catch (err) {
                console.error("Payment Error:", err);
                alert("Terjadi kesalahan sistem/jaringan. Cek log console.");
            } finally {
                btn.disabled = false;
                btn.innerText = "Kirim Bukti Pembayaran";
            }
        }

        async function handleEnrollClick() {
            if (!activeCourse) return;
            
            // AUTH CHECK
            if (!isAuthenticated) {
                openAuthModal();
                return;
            }

            const btn = document.getElementById('btn-enroll-action');
            if(btn) {
                btn.disabled = true;
                btn.innerText = "Memproses...";
            }

            try {
                // Get CSRF Token
                const token = document.querySelector('input[name="_token"]')?.value;
                
                // Modified: Remove /api prefix
                const response = await fetch('/courses/enroll', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token 
                    },
                    body: JSON.stringify({ course_id: activeCourse.id })
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Pendaftaran Berhasil! ' + (data.status === 'pending' ? 'Mohon lakukan pembayaran.' : 'Selamat belajar!'));
                    
                    // Reload Data & Refresh UI
                    await fetchCourses(); 
                    
                    // Refresh Detail View with new status
                    // Find updated course data
                    // We need to fetch again to get updated status or just optimistically update?
                    // Fetching is safer.
                    const updatedCourse = allCourses.find(c => c.id === activeCourse.id);
                    if(updatedCourse) {
                        // Re-open/Refresh detail
                        openCourseDetail(allCourses.indexOf(updatedCourse));
                        
                        // AUTO OPEN PAYMENT IF PENDING
                        if (data.status === 'pending') {
                            setTimeout(() => openPaymentModal(updatedCourse), 500);
                        }
                    }
                } else {
                    alert('Gagal mendaftar: ' + (data.message || 'Error'));
                    if(btn) {
                        btn.disabled = false; 
                        btn.innerText = "Coba Lagi";
                    }
                }
            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan koneksi.');
                if(btn) {
                    btn.disabled = false;
                    btn.innerText = "Error - Coba Lagi";
                }
            }
        }



        function closeCourseDetail() {
            const detailView = document.getElementById('view-course-detail');
            const akademiView = document.getElementById('view-akademi');
            const bottomNav = document.getElementById('bottom-nav');

            // Sembunyikan Detail, Tampilkan Akademi
            detailView.classList.add('hidden');
            akademiView.classList.remove('hidden');

            // Munculkan Bottom Nav Kembali
            if (bottomNav) {
                bottomNav.classList.remove('translate-y-full');
            }
        }




        // --- Helper Modal ---
        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }

        // --- ADMIN ENROLLMENT LOGIC (GLOBAL) ---
        async function fetchEnrollments() {
            const container = document.getElementById('admin-enrollment-list');
            if(!container) return; 
            
            container.innerHTML = '<tr><td colspan="5" class="px-3 py-4 text-center text-gray-400">Loading...</td></tr>';

            try {
                const response = await fetch('/admin/enrollments');
                const data = await response.json();

                if(data.length === 0) {
                    container.innerHTML = '<tr><td colspan="5" class="px-3 py-4 text-center text-gray-400">Belum ada pendaftaran.</td></tr>';
                    return;
                }

                container.innerHTML = '';
                data.forEach(item => {
                    const date = new Date(item.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', hour:'2-digit', minute:'2-digit'});
                    
                    let statusBadge = '';
                    if(item.status === 'active') statusBadge = '<span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-bold">Aktif</span>';
                    else if(item.status === 'pending') statusBadge = '<span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold">Pending</span>';
                    else statusBadge = `<span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-[10px] font-bold">${item.status}</span>`;

                    let proofBtn = '<span class="text-gray-300 text-[10px]">-</span>';
                    if(item.payment_proof) {
                        const proofUrl = `/storage/${item.payment_proof}`;
                        proofBtn = `<button onclick="openProofModal('${proofUrl}')" class="text-blue-600 hover:text-blue-800 font-bold text-[10px] underline">Lihat</button>`;
                    }

                    // Safe Actions without Blade SVG
                    let actions = '';
                    if(item.status === 'pending') {
                         actions = `
                            <div class="flex justify-end gap-1">
                                <button onclick="updateEnrollmentStatus(${item.user_id}, ${item.course_id}, 'active')" class="px-2 py-1 bg-green-50 text-green-600 rounded border border-green-200 hover:bg-green-100 text-[10px] font-bold" title="Terima">
                                    Terima
                                </button>
                                <button onclick="updateEnrollmentStatus(${item.user_id}, ${item.course_id}, 'rejected')" class="px-2 py-1 bg-red-50 text-red-600 rounded border border-red-200 hover:bg-red-100 text-[10px] font-bold" title="Tolak">
                                    Tolak
                                </button>
                            </div>
                         `;
                    } else if (item.status === 'rejected') {
                         actions = '<span class="text-[10px] text-red-500">Ditolak</span>';
                    }

                    const row = `
                        <tr class="hover:bg-gray-50 transition-colors border-b last:border-0 border-gray-50">
                            <td class="px-3 py-2">
                                <p class="font-bold text-gray-900 text-xs truncate max-w-[100px]">${item.user_name}</p>
                                <p class="text-[9px] text-gray-400 truncate max-w-[100px]">${item.user_email}</p>
                            </td>
                            <td class="px-3 py-2">
                                <span class="font-medium text-gray-800 text-xs truncate block max-w-[120px]">${item.course_title}</span>
                                <p class="text-[9px] text-gray-400 capitalize">${item.course_type}</p>
                            </td>
                            <td class="px-3 py-2">${proofBtn}</td>
                            <td class="px-3 py-2 text-center">
                                ${statusBadge}
                                <div class="text-[9px] text-gray-300 mt-0.5">${date}</div>
                            </td>
                            <td class="px-3 py-2 text-right">${actions}</td>
                        </tr>
                    `;
                    container.innerHTML += row;
                });

            } catch(e) {
                console.error(e);
                container.innerHTML = '<tr><td colspan="5" class="px-3 py-4 text-center text-red-400">Gagal memuat.</td></tr>';
            }
        }

        async function updateEnrollmentStatus(userId, courseId, status) {
            const confirmMsg = status === 'active' ? 'Terima pembayaran dan aktifkan kursus?' : 'Tolak pendaftaran ini?';
            if(!confirm(confirmMsg)) return;

            try {
                const token = document.querySelector('input[name="_token"]').value;
                const response = await fetch('/admin/enrollments/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ user_id: userId, course_id: courseId, status: status })
                });

                if(response.ok) {
                    fetchEnrollments(); // Auto refresh
                } else {
                    alert('Gagal memperbarui status.');
                }
            } catch(e) {
                alert('Terjadi kesalahan koneksi.');
            }
        }

        function openProofModal(url) {
            const modal = document.getElementById('proof-modal');
            const img = document.getElementById('proof-image');
            const link = document.getElementById('proof-download-link');
            
            img.src = url;
            link.href = url;
            modal.classList.remove('hidden');
        }

        function closeProofModal() {
            document.getElementById('proof-modal').classList.add('hidden');
        }

        // Initial Load
        document.addEventListener('DOMContentLoaded', () => {
            let savedTab = localStorage.getItem('activeTab');
            const defaultTab = 'home';

            // Check URL Query Param for specifically requested tab (e.g. from Redirects)
            const urlParams = new URLSearchParams(window.location.search);
            const urlTab = urlParams.get('tab');
            if(urlTab) {
                savedTab = urlTab;
                // Optional: Clean URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
            
            // Auto open login tab if there are errors (Overrides URL tab if errors exist)
            @if($errors->any())
                savedTab = 'login';
            @endif

            switchTab(savedTab || defaultTab);
            
            // Load courses from backend via API on port 9000
            fetchCourses();

            // Logika Auto Slider (moved here to combine DOMContentLoaded)
            const slider = document.getElementById('banner-slider');
            const dotsContainer = document.getElementById('slider-dots');

            // --- Guard Clause / Pengecekan Null ---
            if (!slider || !dotsContainer) {
                // If slider elements are not present, just return.
                // This prevents errors if the slider is not on the current view.
                return;
            }

            const dots = dotsContainer.children;
            let currentSlide = 0;
            // Count total slides dynamically (PHP -> JS)
            const totalSlides = {{ count($appSettings->slider_config ?? []) > 0 ? count($appSettings->slider_config) : 3 }};

            // Update dots indicator
            // Update dots indicator
            function updateDots(index) {
                // Get active bg color from themeActiveColor
                const colorMap = {
                    'text-blue-700': 'bg-blue-900',
                    'text-red-700': 'bg-red-900',
                    'text-emerald-700': 'bg-emerald-900',
                    'text-purple-700': 'bg-purple-900',
                    'text-gray-900': 'bg-gray-900'
                };
                const activeDotClass = colorMap[themeActiveColor] || 'bg-blue-900';

                for (let i = 0; i < dots.length; i++) {
                    if (i === index) {
                        dots[i].classList.remove('bg-gray-300', 'w-2', 'h-2');
                        dots[i].classList.add(activeDotClass, 'w-2.5', 'h-2.5');
                    } else {
                        dots[i].classList.add('bg-gray-300', 'w-2', 'h-2');
                        dots[i].classList.remove(activeDotClass, 'w-2.5', 'h-2.5');
                    }
                }
            }


            // Fungsi scroll otomatis
            setInterval(() => {
                // Cek apakah tab Beranda sedang aktif (tidak hidden)
                const homeView = document.getElementById('view-home');
                if (homeView && homeView.classList.contains('hidden')) return;

                // Logika geser slide
                currentSlide++;
                if (currentSlide >= totalSlides) {
                    currentSlide = 0;
                    // Kembali ke awal
                    slider.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    // Geser ke kanan (asumsi lebar elemen + gap)
                    if (slider.firstElementChild) {
                        const slideWidth = slider.firstElementChild.getBoundingClientRect().width + 12; // 12px adalah estimasi gap (space-x-3)
                        slider.scrollTo({ left: slideWidth * currentSlide, behavior: 'smooth' });
                    }
                }

                updateDots(currentSlide);
            }, 3000); // Geser setiap 3 detik
        }); 

        // --- PWA Service Worker Registration ---
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(err => {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>
