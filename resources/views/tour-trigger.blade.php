{{-- Load Shepherd.js CSS from CDN --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@11.2.0/dist/css/shepherd.css">
<script src="https://cdn.jsdelivr.net/npm/shepherd.js@11.2.0/dist/js/shepherd.min.js"></script>
{{-- Apply Custom Colors via CSS Variables --}}
@if (
    $headerColor ||
        $primaryButtonColor ||
        $secondaryButtonColor ||
        $textColor ||
        $backgroundColor ||
        $contentBackgroundColor ||
        $primaryButtonHoverColor ||
        $secondaryButtonHoverColor ||
        $footerBackgroundColor ||
        $primaryButtonTextColor ||
        $secondaryButtonTextColor ||
        $footerBorderColor)
    <style>
        :root {
            @if ($headerColor)
                --tour-header-color: {{ $headerColor }};
            @endif

            @if ($primaryButtonColor)
                --tour-primary-btn-color: {{ $primaryButtonColor }};
            @endif

            @if ($secondaryButtonColor)
                --tour-secondary-btn-color: {{ $secondaryButtonColor }};
            @endif

            @if ($textColor)
                --tour-text-color: {{ $textColor }};
            @endif

            @if ($backgroundColor)
                --tour-background-color: {{ $backgroundColor }};
            @endif

            @if ($contentBackgroundColor)
                --tour-content-bg-color: {{ $contentBackgroundColor }};
            @endif

            @if ($primaryButtonHoverColor)
                --tour-primary-btn-hover: {{ $primaryButtonHoverColor }};
            @endif

            @if ($secondaryButtonHoverColor)
                --tour-secondary-btn-hover: {{ $secondaryButtonHoverColor }};
            @endif

            @if ($footerBackgroundColor)
                --tour-footer-bg-color: {{ $footerBackgroundColor }};
            @endif

            @if ($primaryButtonTextColor)
                --tour-primary-btn-text: {{ $primaryButtonTextColor }};
            @endif

            @if ($secondaryButtonTextColor)
                --tour-secondary-btn-text: {{ $secondaryButtonTextColor }};
            @endif

            @if ($footerBorderColor)
                --tour-footer-border: {{ $footerBorderColor }};
            @endif
        }
    </style>
@endif

{{-- Pass dynamic tour steps to JavaScript --}}
<script>
    window.dynamicTourSteps = @json($tourSteps ?? []);
    window.navigationMap = @json($navigationMap ?? []);
    window.customWelcomeStep = @json($welcomeStep ?? null);
    window.customFinishStep = @json($finishStep ?? null);
</script>

<script>
    // Add data-tour attributes to navigation items after DOM loads
    document.addEventListener('DOMContentLoaded', function() {
        // Function to add data-tour attribute to navigation items
        function addTourAttributes() {
            // Find navigation items by their text content
            const navItems = document.querySelectorAll(
                '.fi-sidebar-item, .fi-sidebar-group, [role="menuitem"]');

            navItems.forEach(item => {
                const text = item.textContent.trim();
                const link = item.querySelector('a');
                const target = link || item;

                // Use dynamic navigation map from resources
                const navigationMap = window.navigationMap || {};

                // Check dynamic mappings first
                let matched = false;
                Object.entries(navigationMap).forEach(([tourId, navText]) => {
                    if (text.includes(navText) || text === navText) {
                        target.setAttribute('data-tour', tourId);
                        matched = true;
                    }
                });

                // If not matched, try static fallback mappings
                if (!matched) {
                    // Merchants Main (القسم الرئيسي)
                    if (text.includes('التجار') && !text.includes('محافظ') && !text.includes(
                            'كشوفات') && !
                        text.includes('منتجات') && !text.includes('الطلبات')) {
                        target.setAttribute('data-tour', 'merchants-main');
                    }

                    // Specific Merchant Resources
                    if (text === 'التجار' || text.includes('قائمة التجار')) {
                        target.setAttribute('data-tour', 'merchants-list');
                    }

                    if (text.includes('محافظ التجار')) {
                        target.setAttribute('data-tour', 'merchant-wallets');
                    }

                    if (text.includes('منتجات التجار')) {
                        target.setAttribute('data-tour', 'merchant-products');
                    }

                    if (text.includes('كشوفات حسابات التجار') || text.includes('كشوفات الحساب')) {
                        target.setAttribute('data-tour', 'merchant-statements');
                    }

                    if (text.includes('الطلبات') && !text.includes('حدود')) {
                        target.setAttribute('data-tour', 'merchant-orders');
                    }

                    // Financial Group Header
                    if (text.includes('القيود & المالية') || text.includes('القيود والمالية')) {
                        item.setAttribute('data-tour', 'financial-group');
                    }

                    // Account Entries
                    if (text === 'القيود' || (text.includes('القيود') && !text.includes('المالية') && !
                            text
                            .includes('&'))) {
                        target.setAttribute('data-tour', 'account-entries');
                    }

                    // Financial Transactions
                    if (text.includes('الحركات المالية')) {
                        target.setAttribute('data-tour', 'payment-transactions');
                    }

                    // Personal Budget Group Header
                    if (text.includes('الميزانية الشخصية')) {
                        item.setAttribute('data-tour', 'budget-group');
                    }

                    // Budgets
                    if (text === 'الميزانيات' || (text.includes('الميزانيات') && !text.includes(
                            'فئات'))) {
                        target.setAttribute('data-tour', 'budgets');
                    }

                    // Budget Categories
                    if (text.includes('فئات الميزانية')) {
                        target.setAttribute('data-tour', 'budget-categories');
                    }

                    // Settings Group Header
                    if (text === 'الإعدادات' || (text.includes('الإعدادات') && !text.includes(
                                'المالية') &&
                            !text.includes('البيانات') && !text.includes('الجولة'))) {
                        item.setAttribute('data-tour', 'settings-group');
                    }

                    // Financial Settings
                    if (text.includes('الإعدادات المالية')) {
                        target.setAttribute('data-tour', 'financial-settings');
                    }

                    // Personal Data Management
                    if (text.includes('إدارة البيانات الشخصية')) {
                        target.setAttribute('data-tour', 'personal-data');
                    }

                    // Tour Steps Management
                    if (text.includes('خطوات الجولة') || text.includes('الجولة التعريفية')) {
                        target.setAttribute('data-tour', 'tour-steps');
                    }
                }
            });

        }

        // Initial run
        setTimeout(addTourAttributes, 500);

        // Re-run when Livewire updates the DOM (for SPAs)
        document.addEventListener('livewire:navigated', () => {
            setTimeout(addTourAttributes, 300);
        });

        // Fallback: Watch for DOM mutations
        const observer = new MutationObserver(function(mutations) {
            // Debounce the function call
            clearTimeout(window.tourAttributeTimeout);
            window.tourAttributeTimeout = setTimeout(addTourAttributes, 200);
        });

        const sidebar = document.querySelector('.fi-sidebar') || document.querySelector('.fi-sidebar-item') ||
            document.querySelector('.fi-sidebar-sub-group-items');
        if (sidebar) {
            observer.observe(sidebar, {
                childList: true,
                subtree: true
            });
        }
    });
</script>
