import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../core/storage/hive_storage.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final _storage = const FlutterSecureStorage();
  int _currentIndex = 3;

  void _onNavigation(int idx) {
    if (idx == 3) return;
    setState(() => _currentIndex = idx);
    if (idx == 0) context.go('/home');
    if (idx == 1) context.go('/catalog');
    if (idx == 2) context.go('/cart');
  }

  void _handleLogout() async {
    await _storage.delete(key: 'auth_token');
    if (mounted) {
      context.go('/login');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('MY ACCOUNT'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            
            // User brief card
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: Row(
                children: [
                  Container(
                    width: 50,
                    height: 50,
                    decoration: BoxDecoration(
                      color: const Color(0xFFFF3F6C).withOpacity(0.08),
                      shape: BoxShape.circle,
                    ),
                    child: const Center(
                      child: Text(
                        'AG',
                        style: TextStyle(fontSize: 16, fontWeight: FontWeight.w950, color: Color(0xFFFF3F6C)),
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Asim Gee', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 14)),
                        SizedBox(height: 4),
                        Text('asimgee105@gmail.com', style: TextStyle(color: Colors.grey, fontSize: 11, fontWeight: FontWeight.bold)),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Profile Options Menu
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: Column(
                children: [
                  ListTile(
                    leading: const Icon(Icons.shopping_bag_outlined, color: Colors.grey),
                    title: const Text('Orders', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                    trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: Colors.grey),
                    onTap: () => context.go('/orders'),
                  ),
                  const Divider(height: 1, indent: 16, endIndent: 16),
                  ListTile(
                    leading: const Icon(Icons.card_giftcard_outlined, color: Colors.grey),
                    title: const Text('Gift Cards', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                    trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: Colors.grey),
                    onTap: () => context.go('/gift-cards'),
                  ),
                  const Divider(height: 1, indent: 16, endIndent: 16),
                  ListTile(
                    leading: const Icon(Icons.stars_outlined, color: Colors.grey),
                    title: const Text('Aura Insider', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                    trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: Colors.grey),
                    onTap: () => context.go('/insider'),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Other info / Log out
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: Column(
                children: [
                  ListTile(
                    leading: const Icon(Icons.info_outline, color: Colors.grey),
                    title: const Text('About Aura Store', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                    trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: Colors.grey),
                    onTap: () {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Aura Enterprise v1.0.0')),
                      );
                    },
                  ),
                  const Divider(height: 1, indent: 16, endIndent: 16),
                  ListTile(
                    leading: const Icon(Icons.logout, color: Colors.red),
                    title: const Text('Logout', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.red)),
                    onTap: _handleLogout,
                  ),
                ],
              ),
            ),

          ],
        ),
      ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: _onNavigation,
        type: BottomNavigationBarType.fixed,
        selectedItemColor: const Color(0xFFFF3F6C),
        unselectedItemColor: Colors.grey,
        selectedLabelStyle: const TextStyle(fontSize: 9, fontWeight: FontWeight.bold),
        unselectedLabelStyle: const TextStyle(fontSize: 9, fontWeight: FontWeight.bold),
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home_outlined), activeIcon: Icon(Icons.home), label: 'Home'),
          BottomNavigationBarItem(icon: Icon(Icons.search), label: 'Search'),
          BottomNavigationBarItem(icon: Icon(Icons.shopping_bag_outlined), activeIcon: Icon(Icons.shopping_bag), label: 'Cart'),
          BottomNavigationBarItem(icon: Icon(Icons.person_outline), activeIcon: Icon(Icons.person), label: 'Profile'),
        ],
      ),
    );
  }
}
