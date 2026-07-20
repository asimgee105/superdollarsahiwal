import 'package:flutter/material.dart';
import '../../core/theme/app_theme.dart';

class MaintenanceScreen extends StatelessWidget {
  const MaintenanceScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: Colors.white,
      body: Padding(
        padding: EdgeInsets.all(24.0),
        child: Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Color(0xFFFAFAFA),
                  shape: BoxShape.circle,
                ),
                child: Icon(Icons.build_circle_outlined, color: AppTheme.primaryColor, size: 64),
              ),
              SizedBox(height: 24),
              Text(
                'SYSTEM UNDER MAINTENANCE',
                style: TextStyle(fontWeight: FontWeight.w900, fontSize: 13, letterSpacing: 1.0),
              ),
              SizedBox(height: 8),
              Text(
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
