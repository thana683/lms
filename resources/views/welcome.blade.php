<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>
        <meta name="description" content="{{ config('app.name') }} — สถาบันฝึกอบรมและพัฒนาศักยภาพจิตใจระดับมาตรฐานสากล">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-[#f7f5ef] text-[#1c1c1a]">
        <header class="border-b border-[#1c2b4a]/10 bg-white/90 backdrop-blur sticky top-0 z-50">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 flex items-center justify-between h-20">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }} logo" class="h-11 w-11 object-contain">
                    <div class="leading-tight">
                        <p class="font-semibold tracking-wide text-[#1c2b4a] text-base">{{ config('app.name') }}</p>
                        <p class="text-[11px] uppercase tracking-[0.2em] text-[#8a7530]">Excellence in Willpower Development</p>
                    </div>
                </div>

                <nav class="hidden lg:flex items-center gap-8 text-sm font-medium text-[#1c2b4a]">
                    <a href="#programs" class="hover:text-[#8a7530] transition-colors">Programs</a>
                    <a href="#about" class="hover:text-[#8a7530] transition-colors">About</a>
                    <a href="#contact" class="hover:text-[#8a7530] transition-colors">Contact</a>
                </nav>

                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-5 py-2 rounded-sm bg-[#1c2b4a] text-white text-sm font-medium hover:bg-[#13203a] transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 rounded-sm text-sm font-medium text-[#1c2b4a] border border-[#1c2b4a]/20 hover:border-[#1c2b4a]/50 transition-colors">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2 rounded-sm bg-[#8a7530] text-white text-sm font-medium hover:bg-[#6f5e26] transition-colors">
                                    Enroll
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <main>
            <section class="relative overflow-hidden bg-[#0f1b33]">
                <div class="absolute inset-0 opacity-[0.06] bg-[radial-gradient(circle_at_20%_20%,white,transparent_45%)]"></div>
                <div class="relative mx-auto max-w-7xl px-6 lg:px-8 py-24 lg:py-32 grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <p class="uppercase tracking-[0.3em] text-[#c9a94f] text-xs font-semibold mb-6">A World-Leading Institute of Willpower Science</p>
                        <h1 class="text-4xl lg:text-6xl font-semibold text-white leading-tight mb-6">
                            {{ config('app.name') }}
                        </h1>
                        <p class="text-lg text-[#c7cede] leading-relaxed max-w-xl mb-10">
                            Advancing the discipline of mental resilience and meditative mastery through rigorous curricula,
                            evidence-based practice, and a global community of certified instructors.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="#programs" class="inline-flex items-center px-7 py-3 rounded-sm bg-[#c9a94f] text-[#0f1b33] font-semibold hover:bg-[#e0be62] transition-colors">
                                Explore Programs
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-7 py-3 rounded-sm border border-white/30 text-white font-semibold hover:border-white/70 transition-colors">
                                    Apply Now
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="flex justify-center lg:justify-end">
                        <div class="rounded-full bg-white/5 border border-white/10 p-10">
                            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="w-56 h-56 lg:w-72 lg:h-72 object-contain drop-shadow-2xl">
                        </div>
                    </div>
                </div>
            </section>

            <section class="border-y border-[#1c2b4a]/10 bg-white">
                <div class="mx-auto max-w-7xl px-6 lg:px-8 py-12 grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                    <div>
                        <p class="text-3xl font-semibold text-[#1c2b4a]">40+</p>
                        <p class="text-sm text-[#5b6172] mt-1">Years of Instruction</p>
                    </div>
                    <div>
                        <p class="text-3xl font-semibold text-[#1c2b4a]">100,000+</p>
                        <p class="text-sm text-[#5b6172] mt-1">Certified Graduates</p>
                    </div>
                    <div>
                        <p class="text-3xl font-semibold text-[#1c2b4a]">50+</p>
                        <p class="text-sm text-[#5b6172] mt-1">Branch Campuses</p>
                    </div>
                    <div>
                        <p class="text-3xl font-semibold text-[#1c2b4a]">12</p>
                        <p class="text-sm text-[#5b6172] mt-1">Countries Reached</p>
                    </div>
                </div>
            </section>

            <section id="programs" class="mx-auto max-w-7xl px-6 lg:px-8 py-24">
                <div class="max-w-2xl mb-14">
                    <p class="uppercase tracking-[0.25em] text-[#8a7530] text-xs font-semibold mb-3">Academic Programs</p>
                    <h2 class="text-3xl lg:text-4xl font-semibold text-[#1c2b4a]">A structured path from foundation to mastery</h2>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="p-8 bg-white border border-[#1c2b4a]/10 rounded-sm hover:shadow-lg transition-shadow">
                        <div class="w-10 h-10 rounded-full bg-[#c9a94f]/15 text-[#8a7530] flex items-center justify-center font-semibold mb-6">1</div>
                        <h3 class="font-semibold text-lg text-[#1c2b4a] mb-2">Foundation Certificate</h3>
                        <p class="text-sm text-[#5b6172] leading-relaxed">Core principles of meditative discipline and willpower training for new students.</p>
                    </div>
                    <div class="p-8 bg-white border border-[#1c2b4a]/10 rounded-sm hover:shadow-lg transition-shadow">
                        <div class="w-10 h-10 rounded-full bg-[#c9a94f]/15 text-[#8a7530] flex items-center justify-center font-semibold mb-6">2</div>
                        <h3 class="font-semibold text-lg text-[#1c2b4a] mb-2">Instructor Diploma</h3>
                        <p class="text-sm text-[#5b6172] leading-relaxed">Advanced coursework and supervised practicum for graduates pursuing certification to teach.</p>
                    </div>
                    <div class="p-8 bg-white border border-[#1c2b4a]/10 rounded-sm hover:shadow-lg transition-shadow">
                        <div class="w-10 h-10 rounded-full bg-[#c9a94f]/15 text-[#8a7530] flex items-center justify-center font-semibold mb-6">3</div>
                        <h3 class="font-semibold text-lg text-[#1c2b4a] mb-2">Senior Mastery Track</h3>
                        <p class="text-sm text-[#5b6172] leading-relaxed">Research-driven specialization for senior instructors shaping the institute's curriculum.</p>
                    </div>
                </div>
            </section>

            <section id="about" class="bg-[#1c2b4a]">
                <div class="mx-auto max-w-7xl px-6 lg:px-8 py-24 grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <p class="uppercase tracking-[0.25em] text-[#c9a94f] text-xs font-semibold mb-3">Our Mission</p>
                        <h2 class="text-3xl lg:text-4xl font-semibold text-white mb-6">Building disciplined minds, one graduate at a time</h2>
                        <p class="text-[#c7cede] leading-relaxed mb-6">
                            {{ config('app.name') }} sets the global standard for willpower and meditation education,
                            combining time-tested contemplative methods with a rigorous, auditable academic structure —
                            enrollment, coursework, examination, and certification, all governed with the same standards
                            expected of a leading university.
                        </p>
                        <ul class="space-y-3 text-[#c7cede] text-sm">
                            <li class="flex items-start gap-3"><span class="mt-1 w-1.5 h-1.5 rounded-full bg-[#c9a94f] shrink-0"></span> Standardized, examinable curriculum across every branch</li>
                            <li class="flex items-start gap-3"><span class="mt-1 w-1.5 h-1.5 rounded-full bg-[#c9a94f] shrink-0"></span> Verified instructor licensing and continuing accreditation</li>
                            <li class="flex items-start gap-3"><span class="mt-1 w-1.5 h-1.5 rounded-full bg-[#c9a94f] shrink-0"></span> Transparent academic records and enrollment reporting</li>
                        </ul>
                    </div>
                    <div class="rounded-sm overflow-hidden border border-white/10 bg-white/5 p-10 flex items-center justify-center">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="w-48 h-48 object-contain opacity-90">
                    </div>
                </div>
            </section>
        </main>

        <footer id="contact" class="bg-[#0f1b33] border-t border-white/10">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 py-14 grid md:grid-cols-3 gap-10 text-[#c7cede] text-sm">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-9 w-9 object-contain">
                        <p class="font-semibold text-white">{{ config('app.name') }}</p>
                    </div>
                    <p class="leading-relaxed">A world-leading institute for willpower and meditation education.</p>
                </div>
                <div>
                    <p class="font-semibold text-white mb-3">Quick Links</p>
                    <ul class="space-y-2">
                        <li><a href="#programs" class="hover:text-white transition-colors">Programs</a></li>
                        <li><a href="#about" class="hover:text-white transition-colors">About</a></li>
                        @if (Route::has('login'))
                            <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Student Login</a></li>
                        @endif
                    </ul>
                </div>
                <div>
                    <p class="font-semibold text-white mb-3">Contact</p>
                    <p>info@willpower.institute</p>
                </div>
            </div>
            <div class="border-t border-white/10 py-6 text-center text-xs text-[#8892a8]">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </footer>
    </body>
</html>
