import 'package:flutter/material.dart';
import '../../core/theme/app_theme.dart';

class MaintenanceScreen extends StatelessWidget {
  const MaintenanceScreen({super.key});

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
                child: const Icon(Icons.build_circle_outlined, color: AppTheme.primaryColor, size: 64),
              ),
              const SizedBox(height: 24),
              const Text(
                'SYSTEM UNDER MAINTENANCE',
                style: TextStyle(fontWeight: FontWeight.w900, fontSize: 13, letterSpacing: 1.0),
              ),
              const SizedBox(height: 8),
              const Text(
                'Our servers are undergoing scheduled database optimization and configuration upgrades. We will be back shortly with a smoother shopping experience.',
                style: TextStyle(fontSize: 10, color: Colors.grey, height: 1.5),
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
