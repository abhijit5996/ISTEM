{{-- Cinematic Institutional Hero - Research Centre Landing --}}
<section class="institutional-hero" data-institutional-hero data-hero-shell>
    <div class="mx-auto w-full max-w-7xl px-4 md:px-8 py-12 md:py-20">
        <div class="grid items-center gap-12 lg:grid-cols-[1fr_1fr] lg:gap-16">
            {{-- LEFT: Content & Storytelling --}}
            <div class="institutional-hero-content" data-reveal data-hero-copy>
                {{-- Kicker --}}
                <div class="mb-6">
                    <span class="inline-flex items-center rounded-full border border-cyan-300/30 bg-cyan-50/50 px-4 py-2 text-xs font-bold uppercase tracking-widest text-cyan-700 dark:border-cyan-400/30 dark:bg-cyan-500/10 dark:text-cyan-300">
                        Biomaterials Laboratory (BL)
                    </span>
                </div>

                {{-- Main Heading --}}
                <h1 class="institutional-hero-heading">
                    Fostering inclusive, interdisciplinary biomaterials research and sustainable technologies.
                </h1>

                {{-- Description --}}
                <p class="institutional-hero-description">
                    A world-class research ecosystem dedicated to translational innovation in smart diagnostics, therapeutic delivery, regenerative therapies, and sustainable agricultural technologies. We combine cutting-edge science with ethical research practices to create real-world impact.
                </p>

                {{-- Funding Badge --}}
                <div class="my-8 flex items-center gap-4 pb-4 border-b border-slate-200 dark:border-white/10">
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Supported by:</span>
                    <img src="{{ asset('frontend/assets/serb-logo.svg') }}" alt="SERB - Science & Engineering Research Board" class="h-8 w-auto opacity-80 hover:opacity-100 transition-opacity">
                </div>

                {{-- CTA Buttons --}}
                <div class="flex flex-wrap items-center gap-4 pt-4" data-hero-action>
                    <a href="{{ route('web.instruments') }}" class="btn-primary" aria-label="Explore Instruments">
                        <span>Explore Instruments</span>
                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </a>
                    <a href="#research" class="btn-secondary" aria-label="Research Areas">
                        Research Areas
                    </a>
                </div>

                {{-- Stats Row --}}
                <div class="mt-12 grid grid-cols-3 gap-6 border-t border-slate-200 pt-8 dark:border-white/10" data-hero-stat>
                    <article class="institutional-stat">
                        <span class="institutional-stat-value">{{ $ecosystemStats[0]['value'] ?? '24/7' }}</span>
                        <span class="institutional-stat-label">Research Access</span>
                    </article>
                    <article class="institutional-stat">
                        <span class="institutional-stat-value">{{ $ecosystemStats[1]['value'] ?? 'Real-time' }}</span>
                        <span class="institutional-stat-label">Queue Visibility</span>
                    </article>
                    <article class="institutional-stat">
                        <span class="institutional-stat-value">{{ $ecosystemStats[2]['value'] ?? 'Always On' }}</span>
                        <span class="institutional-stat-label">System Status</span>
                    </article>
                </div>
            </div>

            {{-- RIGHT: Immersive Visual --}}
            <div class="institutional-hero-visual" data-parallax-hero data-hero-visual>
                <div class="institutional-hero-image-wrap">
                    {{-- Gradient Backdrop --}}
                    <div class="institutional-hero-backdrop"></div>

                    {{-- Main Image --}}
                    <img
                        src="{{ $heroInstrument->image_url ?? asset('frontend/assets/hero-lab-DpylzpE1.jpg') }}"
                        alt="Biomaterials Laboratory Research Team"
                        class="institutional-hero-img"
                    >

                    {{-- Overlay Cards --}}
                    <div class="institutional-hero-card institutional-hero-card-top">
                        <span class="card-label">Research Focus</span>
                        <span class="card-value">Translational Impact</span>
                    </div>

                    <div class="institutional-hero-card institutional-hero-card-bottom">
                        <span class="card-label">Instruments Available</span>
                        <span class="card-value">{{ $instrumentCount ?? '15+' }} Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Parallax effect for hero image */
@media (min-width: 1024px) {
    [data-parallax-hero] {
        perspective: 1000px;
    }

    [data-parallax-hero] img {
        will-change: transform;
    }
}
</style>
