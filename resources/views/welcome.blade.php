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
    <body class="antialiased bg-white text-[#0a0a0a]" style="font-feature-settings: normal;">
        {{-- Top Navigation Bar --}}
        <header class="bg-white border-b border-[#e9e8e7]">
            <div class="mx-auto max-w-[1280px] px-6 lg:px-8 h-16 lg:h-[72px] flex items-center justify-between">
                <a href="/" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-8 w-8 object-contain rounded-[2px]">
                    <span class="text-[20px] font-semibold tracking-[-0.4px]">{{ config('app.name') }}</span>
                </a>

                <nav class="hidden lg:flex items-center gap-8 text-[14px] font-medium">
                    <a href="#programs" class="hover:text-[#535250] transition-colors">Programs</a>
                    <a href="#about" class="hover:text-[#535250] transition-colors">About</a>
                    <a href="#contact" class="hover:text-[#535250] transition-colors">Contact</a>
                </nav>

                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-6 py-3 rounded-[2px] bg-[#0a0a0a] text-white text-[16px] font-medium hover:bg-[#333333] transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-[14px] font-medium hover:text-[#535250] transition-colors">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 rounded-[2px] bg-[#0a0a0a] text-white text-[16px] font-medium hover:bg-[#333333] transition-colors">
                                    Apply Now
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <main>
            {{-- Centered hero: headline / subtext / CTA row, no image --}}
            <section class="bg-white">
                <div class="mx-auto max-w-[1280px] px-6 lg:px-8 pt-20 pb-24 lg:pt-[120px] lg:pb-[120px] flex flex-col items-center text-center">
                    <h1 class="text-[40px] lg:text-[60px] font-normal leading-[1] tracking-[-1.8px] text-[#0a0a0a] max-w-4xl">
                        A world-leading institute for willpower and meditation mastery
                    </h1>
                    <p class="mt-6 text-[20px] leading-[1.4] text-[#535250] max-w-[600px]">
                        Rigorous curricula, evidence-based practice, and a global community of certified
                        instructors — built to the standards of a leading academic institution.
                    </p>
                    <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                        <a href="#programs" class="inline-flex items-center px-6 py-3 rounded-[2px] bg-[#0a0a0a] text-white text-[16px] font-medium hover:bg-[#333333] transition-colors">
                            Explore Programs
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-3 rounded-[2px] border border-[#0a0a0a] text-[#0a0a0a] text-[16px] font-medium hover:bg-[#e9e8e7] transition-colors">
                                Apply Now
                            </a>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Stats row --}}
            <section class="bg-white border-t border-[#e0dedc]">
                <div class="mx-auto max-w-[1280px] px-6 lg:px-8 py-16 grid grid-cols-2 lg:grid-cols-4 gap-y-10 text-center">
                    <div>
                        <p class="text-[48px] font-normal tracking-[-0.64px] text-[#0a0a0a]">40+</p>
                        <p class="mt-2 text-[16px] text-[#535250]">Years of Instruction</p>
                    </div>
                    <div>
                        <p class="text-[48px] font-normal tracking-[-0.64px] text-[#0a0a0a]">100K+</p>
                        <p class="mt-2 text-[16px] text-[#535250]">Certified Graduates</p>
                    </div>
                    <div>
                        <p class="text-[48px] font-normal tracking-[-0.64px] text-[#0a0a0a]">50+</p>
                        <p class="mt-2 text-[16px] text-[#535250]">Branch Campuses</p>
                    </div>
                    <div>
                        <p class="text-[48px] font-normal tracking-[-0.64px] text-[#0a0a0a]">12</p>
                        <p class="mt-2 text-[16px] text-[#535250]">Countries Reached</p>
                    </div>
                </div>
            </section>

            {{-- Dark section band: headline + category pills + program cards --}}
            <section id="programs" class="bg-[#1f1f1e] py-16 lg:py-20">
                <div class="mx-auto max-w-[1280px] px-6 lg:px-8">
                    <h2 class="text-[32px] lg:text-[40px] font-normal leading-[1.1] tracking-[-0.8px] text-white text-center max-w-2xl mx-auto">
                        A structured path from foundation to mastery
                    </h2>

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-2">
                        <span class="inline-flex items-center rounded-full px-4 py-2 text-[14px] font-medium bg-[#0a0a0a] text-white">Foundation</span>
                        <span class="inline-flex items-center rounded-full px-4 py-2 text-[14px] font-medium text-white/60">Instructor Diploma</span>
                        <span class="inline-flex items-center rounded-full px-4 py-2 text-[14px] font-medium text-white/60">Senior Mastery</span>
                    </div>

                    <div class="mt-12 grid md:grid-cols-3 gap-6">
                        <div class="bg-white rounded-[2px] p-6">
                            <p class="text-[14px] font-medium text-[#949189]">01</p>
                            <h3 class="mt-2 text-[24px] font-normal tracking-[-0.4px] text-[#0a0a0a]">Foundation Certificate</h3>
                            <p class="mt-3 text-[16px] leading-[1.5] text-[#535250]">Core principles of meditative discipline and willpower training for new students.</p>
                        </div>
                        <div class="bg-white rounded-[2px] p-6">
                            <p class="text-[14px] font-medium text-[#949189]">02</p>
                            <h3 class="mt-2 text-[24px] font-normal tracking-[-0.4px] text-[#0a0a0a]">Instructor Diploma</h3>
                            <p class="mt-3 text-[16px] leading-[1.5] text-[#535250]">Advanced coursework and supervised practicum for graduates pursuing teaching certification.</p>
                        </div>
                        <div class="bg-white rounded-[2px] p-6">
                            <p class="text-[14px] font-medium text-[#949189]">03</p>
                            <h3 class="mt-2 text-[24px] font-normal tracking-[-0.4px] text-[#0a0a0a]">Senior Mastery Track</h3>
                            <p class="mt-3 text-[16px] leading-[1.5] text-[#535250]">Research-driven specialization for senior instructors shaping the institute's curriculum.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Two-column text + CTA near bottom --}}
            <section id="about" class="bg-white py-20 lg:py-[120px]">
                <div class="mx-auto max-w-[1280px] px-6 lg:px-8 grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <h2 class="text-[28px] lg:text-[32px] font-normal tracking-[-0.64px] text-[#0a0a0a]">Building disciplined minds, one graduate at a time</h2>
                        <p class="mt-4 text-[16px] leading-[1.5] text-[#535250]">
                            {{ config('app.name') }} sets the standard for willpower and meditation education —
                            combining time-tested contemplative methods with a rigorous, auditable academic
                            structure: enrollment, coursework, examination, and certification, governed to the
                            same standards expected of a leading university.
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="email" placeholder="you@example.com" class="flex-1 h-12 px-4 rounded-[2px] border border-[#e0dedc] text-[16px] placeholder:text-[#949189] focus:outline-none focus:border-[#333333]">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center h-12 px-6 rounded-[2px] bg-[#0a0a0a] text-white text-[16px] font-medium hover:bg-[#333333] transition-colors whitespace-nowrap">
                                Request Info
                            </a>
                        @endif
                    </div>
                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer id="contact" class="bg-white border-t border-[#e0dedc]">
            <div class="mx-auto max-w-[1280px] px-6 lg:px-8 py-14 grid md:grid-cols-3 gap-10 text-[14px] text-[#535250]">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-6 w-6 object-contain rounded-[2px]">
                        <span class="text-[16px] font-semibold text-[#0a0a0a]">{{ config('app.name') }}</span>
                    </div>
                    <p class="leading-[1.45]">A world-leading institute for willpower and meditation education.</p>
                </div>
                <div>
                    <p class="text-[16px] font-medium text-[#0a0a0a] mb-3">Quick Links</p>
                    <ul class="space-y-2">
                        <li><a href="#programs" class="hover:text-[#0a0a0a] transition-colors">Programs</a></li>
                        <li><a href="#about" class="hover:text-[#0a0a0a] transition-colors">About</a></li>
                        @if (Route::has('login'))
                            <li><a href="{{ route('login') }}" class="hover:text-[#0a0a0a] transition-colors">Student Login</a></li>
                        @endif
                    </ul>
                </div>
                <div>
                    <p class="text-[16px] font-medium text-[#0a0a0a] mb-3">Contact</p>
                    <p>info@willpower.institute</p>
                </div>
            </div>
            <div class="border-t border-[#e0dedc] py-6 text-center text-[12px] text-[#949189]">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </footer>
    </body>
</html>
