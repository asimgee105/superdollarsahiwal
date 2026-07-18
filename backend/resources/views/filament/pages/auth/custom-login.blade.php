<div x-data="{
    subScreen: 'login', // login, forgot, otp, success
    capsLockOn: false,
    otpVal: ['', '', '', '', '', ''],
    init() {
        // Track CapsLock
        window.addEventListener('keydown', (e) => {
            this.capsLockOn = e.getModifierState && e.getModifierState('CapsLock');
        });

        // Initialize background interactive spiderweb grid net
        this.initConstellationNet();

        // Initialize typewriter auto-typing on placeholders on page load
        this.initTypewriterEffect();
    },
    initConstellationNet() {
        const canvas = document.getElementById('web-grid-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });

        const particles = [];
        const particleCount = 85;
        const maxDistance = 120;
        let mouse = { x: null, y: null };

        window.addEventListener('mousemove', (e) => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
        });

        window.addEventListener('mouseleave', () => {
            mouse.x = null;
            mouse.y = null;
        });

        for (let i = 0; i < particleCount; i++) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * height,
                vx: (Math.random() - 0.5) * 1.0,
                vy: (Math.random() - 0.5) * 1.0
            });
        }

        const renderNet = () => {
            ctx.clearRect(0, 0, width, height);

            // Update & Draw dots
            particles.forEach(p => {
                p.x += p.vx;
                p.y += p.vy;

                if (p.x < 0 || p.x > width) p.vx *= -1;
                if (p.y < 0 || p.y > height) p.vy *= -1;

                ctx.beginPath();
                ctx.arc(p.x, p.y, 2, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(99, 102, 241, 0.4)';
                ctx.fill();
            });

            // Connect neighboring dots (Constellation Net lines)
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const dist = Math.hypot(dx, dy);

                    if (dist < maxDistance) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = `rgba(99, 102, 241, ${0.18 * (1 - dist / maxDistance)})`;
                        ctx.lineWidth = 0.8;
                        ctx.stroke();
                    }
                }
            }

            // Draw Spiderman Web (Concentric rings + connecting web lines to cursor)
            if (mouse.x !== null) {
                ctx.strokeStyle = 'rgba(236, 72, 153, 0.08)';
                ctx.lineWidth = 0.8;
                for (let r = 30; r <= 150; r += 30) {
                    ctx.beginPath();
                    ctx.arc(mouse.x, mouse.y, r, 0, Math.PI * 2);
                    ctx.stroke();
                }

                particles.forEach(p => {
                    const dx = p.x - mouse.x;
                    const dy = p.y - mouse.y;
                    const dist = Math.hypot(dx, dy);

                    if (dist < maxDistance * 1.5) {
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(mouse.x, mouse.y);
                        ctx.strokeStyle = `rgba(236, 72, 153, ${0.28 * (1 - dist / (maxDistance * 1.5))})`;
                        ctx.lineWidth = 1.0;
                        ctx.stroke();
                    }
                });
            }

            requestAnimationFrame(renderNet);
        };
        renderNet();
    },
    initTypewriterEffect() {
        const typeWriter = (inputElement, text, delay = 80, callback = null) => {
            if (!inputElement) {
                if (callback) callback();
                return;
            }
            let index = 0;
            inputElement.placeholder = '';

            const type = () => {
                if (index < text.length) {
                    inputElement.placeholder += text.charAt(index);
                    index++;
                    setTimeout(type, delay);
                } else if (callback) {
                    callback();
                }
            };
            type();
        };

        // Trigger placeholder typewriter sequentially on email & password
        setTimeout(() => {
            const emailInput = document.querySelector('input[type=email]') || document.querySelector('input[id*=email]');
            const passInput = document.querySelector('input[type=password]') || document.querySelector('input[id*=password]');

            typeWriter(emailInput, 'please enter the email', 80, () => {
                setTimeout(() => {
                    typeWriter(passInput, 'please enter the password', 80);
                }, 300);
            });
        }, 1200);
    },
    focusNext(e, index) {
        if (e.target.value.length === 1 && index < 5) {
            this.$refs['otp' + (index + 1)].focus();
        }
    },
    submitOtp() {
        this.subScreen = 'success';
    }
}" class="saas-portal">

    <style>
        /* Force outer Filament wrapper resets and eliminate scrollbars */
        html, body {
            overflow: hidden !important;
            height: 100vh !important;
            width: 100vw !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: #030712 !important;
        }

        .fi-simple-layout {
            background-color: transparent !important;
            min-height: 100vh !important;
            height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
            position: relative !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .fi-simple-main-ctn {
            max-width: 440px !important;
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 16px !important;
            z-index: 10 !important;
        }

        /* Re-style Filament's main layout card to our glassmorphism container */
        .fi-simple-main {
            background: rgba(17, 24, 39, 0.75) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.8), inset 0 1px 0 rgba(255,255,255,0.05) !important;
            border-radius: 24px !important;
            padding: 32px !important;
            width: 100% !important;
            position: relative !important;
            transform: none !important;
        }

        /* Screen Wrapper */
        .saas-portal {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        /* Moving Aurora Backdrops */
        .aurora-bg {
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(236, 72, 153, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(56, 189, 248, 0.08) 0%, transparent 60%);
            background-size: 200% 200%;
            filter: blur(80px);
            z-index: 1;
            pointer-events: none;
            animation: auroraMove 15s ease infinite;
        }

        @keyframes auroraMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating particles */
        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: rgba(99, 102, 241, 0.35);
            border-radius: 50%;
            filter: blur(1px);
            z-index: 2;
            pointer-events: none;
            animation: floatParticle 10s infinite ease-in-out;
        }

        @keyframes floatParticle {
            0% { transform: translateY(105vh) translateX(0) scale(0.8); opacity: 0; }
            50% { opacity: 0.7; }
            100% { transform: translateY(-10vh) translateX(60px) scale(1.3); opacity: 0; }
        }

        /* Input Overrides */
        .fi-simple-main input {
            background-color: rgba(3, 7, 18, 0.6) !important;
            border: 1px solid rgba(255,255,255,0.15) !important;
            color: #ffffff !important;
            border-radius: 12px !important;
            padding: 12px 16px !important;
            transition: all 0.3s ease !important;
        }

        .fi-simple-main input:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.25) !important;
        }

        .fi-simple-main button[type=submit], .saas-btn {
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%) !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 12px !important;
            padding: 14px !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35) !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            width: 100% !important;
        }

        .fi-simple-main button[type=submit]:hover, .saas-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.5) !important;
        }

        /* OTP input boxes */
        .otp-input {
            width: 48px;
            height: 48px;
            text-align: center;
            font-size: 20px;
            font-weight: 800;
            border-radius: 10px;
            background: rgba(3, 7, 18, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: white;
            transition: all 0.2s ease;
        }

        .otp-input:focus {
            border-color: #ec4899;
            box-shadow: 0 0 0 2px rgba(236, 72, 153, 0.25);
        }
    </style>

    {{-- Aurora glow backdrop --}}
    <div class="aurora-bg"></div>

    {{-- Floating sparkles --}}
    <div class="particle" style="left: 10%; animation-delay: 0s; animation-duration: 9s;"></div>
    <div class="particle" style="left: 25%; animation-delay: 2s; animation-duration: 12s;"></div>
    <div class="particle" style="left: 45%; animation-delay: 4s; animation-duration: 8s;"></div>
    <div class="particle" style="left: 65%; animation-delay: 1s; animation-duration: 11s;"></div>
    <div class="particle" style="left: 80%; animation-delay: 5s; animation-duration: 10s;"></div>

    {{-- Screen Content Router --}}
    <div class="w-full">
        
        {{-- Screen 1: Login Form --}}
        <div x-show="subScreen === 'login'" class="w-full">
            <div class="flex flex-col items-center mb-6 text-center select-none">
                <h2 class="text-2xl font-black uppercase tracking-widest bg-gradient-to-r from-indigo-400 to-pink-400 bg-clip-text text-transparent">AURA</h2>
                <p class="text-[10px] text-zinc-400 mt-1 uppercase tracking-wider font-bold">Enterprise SaaS Login</p>
            </div>

            <x-filament-panels::form wire:submit="authenticate">
                {{ $this->form }}
                
                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </x-filament-panels::form>

            <div class="flex items-center justify-between mt-6 border-t border-zinc-800 pt-4">
                <button type="button" @click="subScreen = 'forgot'" class="text-xs font-bold text-zinc-400 hover:text-indigo-400 transition-colors">
                    Forgot Password?
                </button>
                <button type="button" @click="subScreen = 'otp'" class="text-xs font-bold text-zinc-400 hover:text-pink-400 transition-colors">
                    Secure OTP Port
                </button>
            </div>
        </div>

        {{-- Screen 2: Forgot Password Screen --}}
        <div x-show="subScreen === 'forgot'" x-cloak class="space-y-6 w-full">
            <div class="text-center select-none">
                <h3 class="text-lg font-black uppercase text-indigo-400 tracking-wider">Reset Keys</h3>
                <p class="text-xs text-zinc-400 mt-1">Enter your admin keys to receive recovery email.</p>
            </div>

            <form class="space-y-4">
                <div>
                    <label class="text-[10px] font-black uppercase tracking-wider text-zinc-400 block mb-1">Email Address</label>
                    <input type="email" required placeholder="admin@domain.com" class="w-full" />
                </div>
                <button type="button" @click="alert('Reset link dispatched!'); subScreen = 'login';" class="w-full saas-btn text-xs font-bold uppercase tracking-wider">
                    Dispatch Recovery Link
                </button>
            </form>

            <div class="text-center mt-4">
                <button type="button" @click="subScreen = 'login'" class="text-xs font-bold text-zinc-500 hover:text-white transition-colors">
                    &larr; Return to Sign In
                </button>
            </div>
        </div>

        {{-- Screen 3: OTP Verification Screen --}}
        <div x-show="subScreen === 'otp'" x-cloak class="space-y-6 w-full">
            <div class="text-center select-none">
                <h3 class="text-lg font-black uppercase text-pink-400 tracking-wider">Secure Verification</h3>
                <p class="text-xs text-zinc-400 mt-1">Submit the 6-digit authentication token.</p>
            </div>

            <div class="flex justify-center gap-2">
                <template x-for="(val, index) in otpVal">
                    <input
                        type="text"
                        maxlength="1"
                        class="otp-input"
                        x-model="otpVal[index]"
                        x-ref="'otp' + index"
                        @input="focusNext($event, index)"
                    />
                </template>
            </div>

            <button type="button" @click="submitOtp()" class="w-full saas-btn text-xs font-bold uppercase tracking-wider">
                Verify Secure Credentials
            </button>

            <div class="text-center mt-4">
                <button type="button" @click="subScreen = 'login'" class="text-xs font-bold text-zinc-500 hover:text-white transition-colors">
                    Cancel Verification
                </button>
            </div>
        </div>

        {{-- Screen 4: Success Screen --}}
        <div x-show="subScreen === 'success'" x-cloak class="text-center space-y-4 py-8 w-full">
            <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500 flex items-center justify-center mx-auto animate-bounce">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3">
                    <path d="M20,6 L9,17 L4,12" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <h3 class="text-xl font-black text-white">ACCESS GRANTED</h3>
            <p class="text-xs text-zinc-400">Verifications complete. Redirecting to workspace panel...</p>
            <button type="button" @click="window.location.href='/admin'" class="saas-btn text-xs font-bold uppercase tracking-wider px-6 mt-4">
                Enter Console
            </button>
        </div>

    </div>

</div>
