import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

// Splash & Onboarding
import '../../features/splash_onboarding/splash_screen.dart';
import '../../features/splash_onboarding/onboarding_screen.dart';
import '../../features/splash_onboarding/selection_configs_screen.dart';
import '../../features/splash_onboarding/welcome_screen.dart';

// Auth Module
import '../../features/auth/login_screen.dart';
import '../../features/auth/forgot_password_screen.dart';
import '../../features/auth/otp_verification_screen.dart';
import '../../features/auth/create_password_screen.dart';
import '../../features/auth/complete_profile_screen.dart';

// Catalog & Search
import '../../features/home/home_screen.dart';
import '../../features/catalog/categories_screen.dart';
import '../../features/catalog/search_screen.dart';
import '../../features/catalog/catalog_screen.dart';
import '../../features/catalog/compare_screen.dart';
import '../../features/catalog/product_detail_screen.dart';
import '../../features/catalog/product_gallery_view.dart';

// Cart & Checkout
import '../../features/cart/cart_screen.dart';
import '../../features/cart/address_book_screen.dart';
import '../../features/checkout/checkout_screen.dart';
import '../../features/checkout/payment_processing_screen.dart';

// Profile & Settings
import '../../features/profile/profile_screen.dart';
import '../../features/profile/edit_profile_screen.dart';
import '../../features/profile/saved_cards_screen.dart';
import '../../features/profile/security_settings_screen.dart';
import '../../features/profile/orders_screen.dart';
import '../../features/profile/track_order_screen.dart';
import '../../features/profile/invoice_screen.dart';
import '../../features/profile/order_action_screen.dart';
import '../../features/profile/gift_cards_screen.dart';
import '../../features/profile/insider_screen.dart';

// Support & AI
import '../../features/support_chat/live_chat_screen.dart';
import '../../features/support_chat/ai_shopping_assistant.dart';
import '../../features/support_chat/faq_policies_screen.dart';
import '../../features/support_chat/blogs_testimonials_screen.dart';

// System Status Diagnostics
import '../../features/system_status/offline_screen.dart';
import '../../features/system_status/maintenance_screen.dart';
import '../../features/system_status/force_update_screen.dart';
import '../../features/system_status/error_diagnostic_screen.dart';

final GoRouter appRouter = GoRouter(
  initialLocation: '/',
  routes: [
    // Splash & Onboarding
    GoRoute(
      path: '/',
      builder: (context, state) => const SplashScreen(),
    ),
    GoRoute(
      path: '/onboarding',
      builder: (context, state) => const OnboardingScreen(),
    ),
    GoRoute(
      path: '/preferences',
      builder: (context, state) => const SelectionConfigsScreen(),
    ),
    GoRoute(
      path: '/welcome',
      builder: (context, state) => const WelcomeScreen(),
    ),

    // Auth Module
    GoRoute(
      path: '/login',
      builder: (context, state) => const LoginScreen(),
    ),
    GoRoute(
      path: '/forgot-password',
      builder: (context, state) => const ForgotPasswordScreen(),
    ),
    GoRoute(
      path: '/otp-verify',
      builder: (context, state) => const OtpVerificationScreen(),
    ),
    GoRoute(
      path: '/create-password',
      builder: (context, state) => const CreatePasswordScreen(),
    ),
    GoRoute(
      path: '/complete-profile',
      builder: (context, state) => const CompleteProfileScreen(),
    ),

    // Home & Catalog
    GoRoute(
      path: '/home',
      builder: (context, state) => const HomeScreen(),
    ),
    GoRoute(
      path: '/categories',
      builder: (context, state) => const CategoriesScreen(),
    ),
    GoRoute(
      path: '/search',
      builder: (context, state) => const SearchScreen(),
    ),
    GoRoute(
      path: '/catalog',
      builder: (context, state) {
        final cat = state.uri.queryParameters['category'];
        return CatalogScreen(initialCategory: cat);
      },
    ),
    GoRoute(
      path: '/compare',
      builder: (context, state) => const CompareScreen(),
    ),
    GoRoute(
      path: '/product/:id',
      builder: (context, state) {
        final id = int.tryParse(state.pathParameters['id'] ?? '1') ?? 1;
        return ProductDetailScreen(productId: id);
      },
    ),
    GoRoute(
      path: '/catalog/detail/:id',
      builder: (context, state) {
        final id = int.tryParse(state.pathParameters['id'] ?? '1') ?? 1;
        return ProductDetailScreen(productId: id);
      },
    ),
    GoRoute(
      path: '/product-gallery',
      builder: (context, state) {
        final extra = state.extra as List<String>? ?? [];
        return ProductGalleryView(images: extra);
      },
    ),

    // Cart & Checkout
    GoRoute(
      path: '/cart',
      builder: (context, state) => const CartScreen(),
    ),
    GoRoute(
      path: '/address-book',
      builder: (context, state) {
        final select = state.uri.queryParameters['select'] == 'true';
        return AddressBookScreen(selectMode: select);
      },
    ),
    GoRoute(
      path: '/checkout',
      builder: (context, state) => const CheckoutScreen(),
    ),
    GoRoute(
      path: '/payment-processing',
      builder: (context, state) {
        final extra = state.extra as Map<String, dynamic>? ?? {};
        final amount = (extra['amount'] as num?)?.toDouble() ?? 0.0;
        final method = (extra['method'] as String?) ?? 'COD';
        return PaymentProcessingScreen(amount: amount, method: method);
      },
    ),

    // Profile & Settings
    GoRoute(
      path: '/profile',
      builder: (context, state) => const ProfileScreen(),
    ),
    GoRoute(
      path: '/edit-profile',
      builder: (context, state) => const EditProfileScreen(),
    ),
    GoRoute(
      path: '/saved-cards',
      builder: (context, state) => const SavedCardsScreen(),
    ),
    GoRoute(
      path: '/security-settings',
      builder: (context, state) => const SecuritySettingsScreen(),
    ),
    GoRoute(
      path: '/orders',
      builder: (context, state) => const OrdersScreen(),
    ),
    GoRoute(
      path: '/track-order/:id',
      builder: (context, state) {
        final id = state.pathParameters['id'] ?? 'ODR-849102';
        return TrackOrderScreen(orderId: id);
      },
    ),
    GoRoute(
      path: '/invoice',
      builder: (context, state) {
        final id = state.extra as String? ?? 'ODR-849102';
        return InvoiceScreen(orderId: id);
      },
    ),
    GoRoute(
      path: '/order-actions',
      builder: (context, state) {
        final id = state.extra as String? ?? 'ODR-849102';
        return OrderActionScreen(orderId: id);
      },
    ),
    GoRoute(
      path: '/gift-cards',
      builder: (context, state) => const GiftCardsScreen(),
    ),
    GoRoute(
      path: '/insider',
      builder: (context, state) => const InsiderScreen(),
    ),

    // Support & AI
    GoRoute(
      path: '/live-chat',
      builder: (context, state) => const LiveChatScreen(),
    ),
    GoRoute(
      path: '/ai-assistant',
      builder: (context, state) => const AIShoppingAssistant(),
    ),
    GoRoute(
      path: '/faq-policies',
      builder: (context, state) => const FaqPoliciesScreen(),
    ),
    GoRoute(
      path: '/lookbooks',
      builder: (context, state) => const BlogsTestimonialsScreen(),
    ),

    // System Diagnostic Statuses
    GoRoute(
      path: '/offline',
      builder: (context, state) => const OfflineScreen(),
    ),
    GoRoute(
      path: '/maintenance',
      builder: (context, state) => const MaintenanceScreen(),
    ),
    GoRoute(
      path: '/force-update',
      builder: (context, state) => const ForceUpdateScreen(),
    ),
    GoRoute(
      path: '/error/:code',
      builder: (context, state) {
        final code = state.pathParameters['code'] ?? '500';
        final msg = state.uri.queryParameters['msg'];
        return ErrorDiagnosticScreen(errorCode: code, customMessage: msg);
      },
    ),
  ],
);
