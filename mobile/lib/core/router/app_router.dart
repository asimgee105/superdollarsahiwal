import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../features/splash_onboarding/splash_screen.dart';
import '../../features/splash_onboarding/onboarding_screen.dart';
import '../../features/auth/login_screen.dart';
import '../../features/auth/complete_profile_screen.dart';
import '../../features/home/home_screen.dart';
import '../../features/catalog/catalog_screen.dart';
import '../../features/catalog/product_detail_screen.dart';
import '../../features/cart/cart_screen.dart';
import '../../features/checkout/checkout_screen.dart';
import '../../features/profile/profile_screen.dart';
import '../../features/profile/orders_screen.dart';
import '../../features/profile/gift_cards_screen.dart';
import '../../features/profile/insider_screen.dart';

final GoRouter appRouter = GoRouter(
  initialLocation: '/',
  routes: [
    GoRoute(
      path: '/',
      builder: (context, state) => const SplashScreen(),
    ),
    GoRoute(
      path: '/onboarding',
      builder: (context, state) => const OnboardingScreen(),
    ),
    GoRoute(
      path: '/login',
      builder: (context, state) => const LoginScreen(),
    ),
    GoRoute(
      path: '/complete-profile',
      builder: (context, state) => const CompleteProfileScreen(),
    ),
    GoRoute(
      path: '/home',
      builder: (context, state) => const HomeScreen(),
    ),
    GoRoute(
      path: '/catalog',
      builder: (context, state) => const CatalogScreen(),
    ),
    GoRoute(
      path: '/product/:id',
      builder: (context, state) {
        final id = int.tryParse(state.pathParameters['id'] ?? '1') ?? 1;
        return ProductDetailScreen(productId: id);
      },
    ),
    GoRoute(
      path: '/cart',
      builder: (context, state) => const CartScreen(),
    ),
    GoRoute(
      path: '/checkout',
      builder: (context, state) => const CheckoutScreen(),
    ),
    GoRoute(
      path: '/profile',
      builder: (context, state) => const ProfileScreen(),
    ),
    GoRoute(
      path: '/orders',
      builder: (context, state) => const OrdersScreen(),
    ),
    GoRoute(
      path: '/gift-cards',
      builder: (context, state) => const GiftCardsScreen(),
    ),
    GoRoute(
      path: '/insider',
      builder: (context, state) => const InsiderScreen(),
    ),
  ],
);
