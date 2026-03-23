<?php $__env->startSection('title', 'Home'); ?>
<?php $__env->startSection('meta_description', 'G&M Autospares - quality used car parts in New Zealand. Browse parts and vehicles we are now dismantling.'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $slides = \App\Models\SliderSlide::where('is_active', true)->orderBy('sort_order')->get();
        $categories = \App\Models\PartCategory::where('is_active', true)->orderBy('sort_order')->get();
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($slides->isNotEmpty()): ?>
        
        <section class="hero-slider travely-hero has-bg-image" aria-label="Hero slider">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="hero-slide <?php echo e($index === 0 ? 'active' : ''); ?>" data-index="<?php echo e($index); ?>">
                    <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url($slide->image)); ?>" alt="<?php echo e($slide->title ?? 'Slide'); ?>">
                    <div class="hero-overlay"></div>
                    <div class="hero-slide-content position-absolute bottom-0 start-0 end-0 p-4 text-white text-start text-md-center">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($slide->title): ?><h2 class="h3 mb-1"><?php echo e($slide->title); ?></h2><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($slide->subtitle): ?><p class="mb-0 opacity-90"><?php echo e($slide->subtitle); ?></p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($slide->link_url && $slide->link_text): ?>
                            <a href="<?php echo e($slide->link_url); ?>" class="btn btn-primary btn-sm mt-2"><?php echo e($slide->link_text); ?></a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($slides->count() > 1): ?>
            <script>
                (function(){
                    var slides = document.querySelectorAll('.hero-slider .hero-slide');
                    var i = 0;
                    setInterval(function(){
                        slides[i].classList.remove('active');
                        i = (i + 1) % slides.length;
                        slides[i].classList.add('active');
                    }, 5000);
                })();
            </script>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php else: ?>
        
        <section class="travely-hero">
            <div class="hero-overlay"></div>
            <div class="hero-inner">
                <h1>Find Quality Auto Parts</h1>
                <p class="lead">Search our catalogue for used car parts across makes and models. Quality parts at fair prices across New Zealand.</p>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="container travely-search-card">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(route('parts.index')); ?>" method="get" class="row g-3 align-items-end">
                    <div class="col-6 col-md">
                        <label for="home-make" class="form-label">Make</label>
                        <input type="text" id="home-make" name="make" class="form-control" placeholder="e.g. Toyota" value="<?php echo e(request('make')); ?>">
                    </div>
                    <div class="col-6 col-md">
                        <label for="home-model" class="form-label">Model</label>
                        <input type="text" id="home-model" name="model" class="form-control" placeholder="e.g. Hilux" value="<?php echo e(request('model')); ?>">
                    </div>
                    <div class="col-6 col-md">
                        <label for="home-year" class="form-label">Year</label>
                        <input type="text" id="home-year" name="year" class="form-control" placeholder="e.g. 2015" value="<?php echo e(request('year')); ?>">
                    </div>
                    <div class="col-6 col-md">
                        <label for="home-category" class="form-label">Category</label>
                        <select id="home-category" name="category" class="form-select">
                            <option value="">All categories</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md">
                        <label for="home-keyword" class="form-label">Keyword</label>
                        <input type="text" id="home-keyword" name="keyword" class="form-control" placeholder="Search parts..." value="<?php echo e(request('keyword')); ?>">
                    </div>
                    <div class="col-12 col-md-auto">
                        <button type="submit" class="btn btn-primary w-100 w-md-auto btn-search">
                            Search parts
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <section class="py-5 bg-light">
        <div class="container">
            <?php
                $featuredParts = \App\Models\Part::where('is_visible', true)->where('is_featured', true)->latest()->take(6)->get();
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($featuredParts->isNotEmpty()): ?>
                <h2 class="section-title">Featured parts</h2>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $featuredParts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('parts.show', $part->slug)); ?>" class="part-card card h-100 text-decoration-none text-dark">
                            <div class="part-card-image card-img-top">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($part->images) && count($part->images)): ?>
                                    <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url($part->images[0])); ?>" alt="<?php echo e($part->title); ?>" loading="lazy" class="w-100 h-100 object-fit-cover">
                                <?php else: ?>
                                    <div class="part-card-placeholder">No image</div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo e($part->title); ?></h5>
                                <p class="card-text small text-body-secondary"><?php echo e($part->category->name); ?></p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($part->price): ?><p class="part-card-price mb-0">$<?php echo e(number_format($part->price, 2)); ?></p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?php echo e(route('parts.index')); ?>" class="btn btn-primary">View all parts</a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php
                $dismantling = \App\Models\Vehicle::where('is_visible', true)->withCount('parts')->latest()->take(4)->get();
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dismantling->isNotEmpty()): ?>
                <h2 class="section-title mt-5 pt-4">Now dismantling</h2>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $dismantling; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('vehicles.show', $vehicle)); ?>" class="vehicle-card card h-100 text-decoration-none text-dark">
                            <div class="vehicle-card-image card-img-top">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($vehicle->images) && count($vehicle->images)): ?>
                                    <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url($vehicle->images[0])); ?>" alt="<?php echo e($vehicle->display_name); ?>" loading="lazy" class="w-100 h-100 object-fit-cover">
                                <?php else: ?>
                                    <div class="part-card-placeholder">No image</div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo e($vehicle->display_name); ?></h5>
                                <p class="card-text small text-body-secondary mb-0"><?php echo e($vehicle->parts_count); ?> parts</p>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?php echo e(route('vehicles.index')); ?>" class="btn btn-outline-primary">View all vehicles</a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/stevepeters/Files/GM/resources/views/home.blade.php ENDPATH**/ ?>