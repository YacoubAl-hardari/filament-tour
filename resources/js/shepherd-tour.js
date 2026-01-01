// Shepherd is loaded globally from the CDN script in the Blade view
const Shepherd = window.Shepherd;

// Static Tour Steps - Only Welcome and Finish
const tourStepsData = [
    // Welcome Step
    {
        id: 'welcome',
        title: '👋 مرحباً بك في جولة النظام!',
        text: '<strong>سنقوم بجولة شاملة لشرح جميع المميزات والأقسام في النظام المالي الخاص بك.</strong><br><br>الجولة تستغرق بضع دقائق فقط وستساعدك على فهم كل ميزة في النظام.',
        attachTo: null,
        position: 'center',
        buttons: [
            { text: 'تخطي الجولة', action: 'cancel', secondary: true },
            { text: 'ابدأ الجولة', action: 'next', secondary: false }
        ]
    },
    
    // Final Step
    {
        id: 'finish',
        title: 'تهانينا! انتهت الجولة',
        text: '<strong>الآن أصبحت جاهزاً لاستخدام النظام!</strong><br><br>لقد تعرفت على جميع مميزات النظام.<br><br>يمكنك إعادة الجولة في أي وقت من خلال النقر على أيقونة 🎓 في الأعلى.',
        attachTo: null,
        position: 'center',
        buttons: [
            { text: 'السابق', action: 'back', secondary: true },
            { text: 'إنهاء الجولة', action: 'complete', secondary: false }
        ]
    }
];

// Initialize tour
export function initializeShepherdTour(resumeFromStep = null) {
    // Create a new tour instance
    const tour = new Shepherd.Tour({
        useModalOverlay: true,
        defaultStepOptions: {
            classes: 'shepherd-theme-custom',
            scrollTo: { behavior: 'smooth', block: 'center' },
            cancelIcon: {
                enabled: true,
                label: 'إغلاق'
            },
            modalOverlayOpeningRadius: 12,
            modalOverlayOpeningPadding: 10
        },
        tourName: 'app-tour'
    });

    // Store original active navigation items
    let originalActiveItems = [];

    // Remove Filament's default active highlighting when tour starts
    tour.on('start', () => {
        // Find all currently active navigation items
        originalActiveItems = Array.from(document.querySelectorAll('.fi-sidebar-item.fi-active, .fi-sidebar-group-item.fi-active, [data-tour].fi-active'));
        
        console.log('Tour started, hiding original active items:', originalActiveItems.length);
        
        // Hide their active state temporarily
        originalActiveItems.forEach(item => {
            item.classList.add('tour-original-active');
            item.classList.remove('fi-active');
        });
    });

    // Highlight only the current tour step's navigation item
    tour.on('show', (event) => {
        if (event.step) {
            const stepId = event.step.id;
            console.log(`\n🎯 Tour Step Changed: "${stepId}"`);
            console.log(`   Step Title: "${event.step.options.title}"`);
            
            localStorage.setItem('shepherd-tour-current-step', stepId);
            localStorage.setItem('shepherd-tour-in-progress', 'true');
            
            // Remove previous tour highlighting
            const previousHighlighted = document.querySelectorAll('.shepherd-tour-active-nav');
            console.log(`   Removing ${previousHighlighted.length} previous highlights`);
            previousHighlighted.forEach(item => {
                item.classList.remove('shepherd-tour-active-nav');
            });
            
            // Add highlighting to current step's navigation item
            setTimeout(() => {
                // Show all available data-tour attributes
                const allTourElements = document.querySelectorAll('[data-tour]');
                console.log(`   Available [data-tour] elements:`, Array.from(allTourElements).map(el => el.getAttribute('data-tour')));
                
                const navItem = document.querySelector(`[data-tour="${stepId}"]`);
                console.log(`   Looking for: [data-tour="${stepId}"]`);
                console.log(`   Found element:`, navItem);
                
                if (navItem) {
                    const navContainer = 
                        navItem.closest('.fi-sidebar-item') || 
                        navItem.closest('.fi-sidebar-group-item') || 
                        navItem.closest('.fi-sidebar-item-button') ||
                        navItem.closest('li') ||
                        navItem.parentElement ||
                        navItem;
                    
                    console.log(`   Nav container:`, navContainer);
                    
                    if (navContainer) {
                        navContainer.classList.add('shepherd-tour-active-nav');
                        if (navItem !== navContainer) {
                            navItem.classList.add('shepherd-tour-active-nav');
                        }
                        navContainer.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'nearest',
                            inline: 'nearest'
                        });
                        console.log(`   ✅ Successfully highlighted: "${stepId}"`);
                    } else {
                        console.warn(`   ❌ Could not find nav container for: "${stepId}"`);
                    }
                } else {
                    console.warn(`   ❌ No nav item found with [data-tour="${stepId}"]`);
                }
            }, 300);
        }
    });

    // Restore original active highlighting when tour completes
    tour.on('complete', () => {
        localStorage.removeItem('shepherd-tour-current-step');
        localStorage.removeItem('shepherd-tour-in-progress');
        localStorage.setItem('shepherd-tour-completed', 'true');
        localStorage.setItem('shepherd-tour-completed-at', new Date().toISOString());
        
        // Remove tour highlighting
        document.querySelectorAll('.shepherd-tour-active-nav').forEach(item => {
            item.classList.remove('shepherd-tour-active-nav');
        });
        
        // Restore original active items
        originalActiveItems.forEach(item => {
            item.classList.remove('tour-original-active');
            item.classList.add('fi-active');
        });
        
        console.log('Tour completed, restored original active items');
    });

    // Restore original active highlighting when tour is cancelled
    tour.on('cancel', () => {
        localStorage.removeItem('shepherd-tour-current-step');
        localStorage.removeItem('shepherd-tour-in-progress');
        
        // Remove tour highlighting
        document.querySelectorAll('.shepherd-tour-active-nav').forEach(item => {
            item.classList.remove('shepherd-tour-active-nav');
        });
        
        // Restore original active items
        originalActiveItems.forEach(item => {
            item.classList.remove('tour-original-active');
            item.classList.add('fi-active');
        });
        
        console.log('Tour cancelled, restored original active items');
    });

    // Merge static steps with dynamic steps from resources
    const dynamicSteps = window.dynamicTourSteps || [];
    
    // Use custom welcome/finish steps if provided, otherwise use defaults
    const welcomeStep = window.customWelcomeStep || tourStepsData[0];
    const finishStep = window.customFinishStep || tourStepsData[1];
    
    // Build complete steps array: Welcome → Dynamic Steps → Finish
    const allSteps = [
        welcomeStep,      // Welcome step (customizable)
        ...dynamicSteps,  // All dynamic steps from resources
        finishStep        // Finish step (customizable)
    ];

    // Add steps from combined data
    allSteps.forEach((stepData, index) => {
        const stepConfig = {
            id: stepData.id,
            title: stepData.title,
            text: stepData.text,
        };

        // Add beforeShowPromise to navigate to resource page if needed
        if (stepData.url) {
            stepConfig.beforeShowPromise = function() {
                return new Promise((resolve) => {
                    const currentUrl = window.location.pathname;
                    const targetUrl = new URL(stepData.url, window.location.origin).pathname;
                    
                    console.log(`\n🚀 Navigation Check for step: ${stepData.id}`);
                    console.log(`   Current URL: ${currentUrl}`);
                    console.log(`   Target URL: ${targetUrl}`);
                    
                    // Check if we're already on the target page
                    if (currentUrl !== targetUrl) {
                        console.log(`   ⚡ Navigating to: ${stepData.url}`);
                        
                        // Use Livewire navigate if available (SPA mode)
                        if (typeof Livewire !== 'undefined' && Livewire.navigate) {
                            Livewire.navigate(stepData.url);
                            
                            // Wait for Livewire navigation to complete
                            document.addEventListener('livewire:navigated', function handler() {
                                document.removeEventListener('livewire:navigated', handler);
                                console.log(`   ✅ Navigation completed, re-detecting elements...`);
                                setTimeout(() => {
                                    autoDetectNavigationElements();
                                    resolve();
                                }, 800);
                            }, { once: true });
                        } else {
                            // Fallback to regular navigation
                            window.location.href = stepData.url;
                        }
                    } else {
                        console.log(`   ✅ Already on target page`);
                        // Already on the page, re-run detection to be sure
                        setTimeout(() => {
                            autoDetectNavigationElements();
                            resolve();
                        }, 300);
                    }
                });
            };
        }

        // Auto-detect and attach to elements
        if (stepData.attachTo) {
            const element = document.querySelector(stepData.attachTo);
            if (element) {
                stepConfig.attachTo = {
                    element: element,
                    on: stepData.position || 'right'
                };
                console.log(`Found element for step ${stepData.id}`);
            } else {
                console.log(`Element not found for step ${stepData.id}, showing in center`);
                // Show in center if element not found
            }
        }

        // Build buttons
        const buttons = [];
        const stepButtons = stepData.buttons || [
            { text: 'السابق', action: 'back', secondary: true },
            { text: 'التالي', action: 'next', secondary: false }
        ];

        stepButtons.forEach(btnData => {
            const button = {
                text: btnData.text,
                secondary: btnData.secondary || false
            };

            // Handle button actions
            if (btnData.action === 'back') {
                button.action = tour.back;
            } else if (btnData.action === 'next') {
                button.action = tour.next;
            } else if (btnData.action === 'cancel') {
                button.action = tour.cancel;
            } else if (btnData.action === 'complete') {
                button.action = tour.complete;
            }

            buttons.push(button);
        });

        stepConfig.buttons = buttons;

        // Add the step to the tour
        tour.addStep(stepConfig);
    });

    return tour;
}

// Auto-detect navigation elements and add data-tour attributes
function autoDetectNavigationElements() {
    console.log('\n🔍 Auto-detecting navigation elements...');
    
    // Use dynamic navigation map from resources (passed from PHP)
    // navigationMap format: { "navigationLabel": "tourStepId" }
    const navigationMap = window.navigationMap || {};
    console.log('   Navigation Map:', navigationMap);

    // Find all navigation items
    const navItems = document.querySelectorAll('.fi-sidebar-item, .fi-sidebar-group, [role="menuitem"], a[href*="/admin"]');
    console.log(`   Found ${navItems.length} navigation items`);
    
    let matchedCount = 0;
    navItems.forEach(item => {
        const text = item.textContent.trim();
        const link = item.querySelector('a') || item;
        
        // Check each mapping: navLabel => stepId
        Object.entries(navigationMap).forEach(([navLabel, stepId]) => {
            if (text.includes(navLabel) || text === navLabel) {
                link.setAttribute('data-tour', stepId);
                matchedCount++;
                console.log(`   ✅ Matched: "${navLabel}" → [data-tour="${stepId}"]`);
            }
        });
    });
    
    console.log(`   Total matched: ${matchedCount} items\n`);
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Auto-detect navigation elements
    autoDetectNavigationElements();
    
    // Re-run detection when navigation updates
    setTimeout(autoDetectNavigationElements, 1000);
    
    // Watch for navigation changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                setTimeout(autoDetectNavigationElements, 100);
            }
        });
    });
    
    const sidebar = document.querySelector('.fi-sidebar');
    if (sidebar) {
        observer.observe(sidebar, {
            childList: true,
            subtree: true
        });
    }

    // Check if user wants to see the tour
    const tourButtons = document.querySelectorAll('[data-shepherd-tour-trigger]');
    tourButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            try {
                const tour = initializeShepherdTour();
                
                // Check if there's a tour in progress
                const inProgress = localStorage.getItem('shepherd-tour-in-progress');
                const currentStepId = localStorage.getItem('shepherd-tour-current-step');
                
                if (inProgress === 'true' && currentStepId) {
                    // Resume from the last step
                    console.log(`Resuming tour from step: ${currentStepId}`);
                    tour.show(currentStepId);
                } else {
                    // Start from beginning
                    tour.start();
                }
            } catch (error) {
                console.error('Error starting tour:', error);
                alert('حدث خطأ أثناء بدء الجولة. يرجى المحاولة مرة أخرى.');
            }
        });
    });

    // Auto-resume tour if in progress (after page navigation)
    const inProgress = localStorage.getItem('shepherd-tour-in-progress');
    const currentStepId = localStorage.getItem('shepherd-tour-current-step');
    
    if (inProgress === 'true' && currentStepId) {
        setTimeout(() => {
            try {
                const tour = initializeShepherdTour();
                console.log(`Auto-resuming tour at step: ${currentStepId}`);
                tour.show(currentStepId);
            } catch (error) {
                console.error('Error auto-resuming tour:', error);
                // Clear invalid tour state
                localStorage.removeItem('shepherd-tour-in-progress');
                localStorage.removeItem('shepherd-tour-current-step');
            }
        }, 1500); // Wait for page to fully load
    }
});


// Export for use in other modules
export default initializeShepherdTour;

