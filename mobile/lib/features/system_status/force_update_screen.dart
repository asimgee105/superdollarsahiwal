import 'package:flutter/material.dart';
import '../../core/theme/app_theme.dart';

class ForceUpdateScreen extends StatelessWidget {
  const ForceUpdateScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(20),
                decoration: const BoxDecoration(
                  color: Color(0xFFFAFAFA),
                  shape: BoxShape.circle,
                ),
                child: const Icon(Icons.system_update_outlined, color: AppTheme.primaryColor, size: 64),
              ),
              const SizedBox(height: 24),
              const Text(
                'CRITICAL UPDATE REQUIRED',
                style: TextStyle(fontWeight: FontWeight.w900, fontSize: 13, letterSpacing: 1.0),
              ),
              const SizedBox(height: 8),
              const Text(
                'A new version of Super Dollar containing security fixes and performance updates is available. Please update your application to continue shopping.',
                style: TextStyle(fontSize: 10, color: Colors.grey, height: 1.5),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 32),
              ElevatedButton(
                onPressed: () {
                  // Simulate opening App Store / Google Play Store link
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primaryColor,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: const Text('UPDATE NOW', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900)),
              )
            ],
          ),
        ),
      ),
    );
  }
}
