import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/storage/hive_storage.dart';
import '../../core/theme/app_theme.dart';

class SelectionConfigsScreen extends StatefulWidget {
  const SelectionConfigsScreen({super.key});

  @override
  State<SelectionConfigsScreen> createState() => _SelectionConfigsScreenState();
}

class _SelectionConfigsScreenState extends State<SelectionConfigsScreen> {
  String _selectedLanguage = 'English';
  String _selectedCountry = 'Pakistan';
  String _selectedCurrency = 'PKR';

  final List<String> _languages = ['English', 'Urdu', 'Arabic'];
  final List<String> _countries = ['Pakistan', 'UAE', 'Saudi Arabia', 'USA'];
  final Map<String, String> _currencies = {
    'Pakistan': 'PKR',
    'UAE': 'AED',
    'Saudi Arabia': 'SAR',
    'USA': 'USD',
  };

  void _saveConfigs() {
    HiveStorage.settings.put('language', _selectedLanguage);
    HiveStorage.settings.put('country', _selectedCountry);
    HiveStorage.settings.put('currency', _selectedCurrency);
    HiveStorage.settings.put('configs_selected', true);
    
    // Smooth navigation to welcome
    context.go('/welcome');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 30),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const SizedBox(height: 20),
              // App Logo
              Center(
                child: Container(
                  width: 70,
                  height: 70,
                  decoration: BoxDecoration(
                    color: AppTheme.primaryColor,
                    borderRadius: BorderRadius.circular(16),
                    boxShadow: [
                      BoxShadow(
                        color: AppTheme.primaryColor.withOpacity(0.3),
                        blurRadius: 15,
                        offset: const Offset(0, 8),
                      )
                    ],
                  ),
                  child: const Center(
                    child: Text(
                      'S',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 36,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 24),
              const Center(
                child: Text(
                  'PREFERENCES',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w900,
                    letterSpacing: 2.0,
                    color: Color(0xFFFF3F6C),
                  ),
                ),
              ),
              const SizedBox(height: 8),
              const Center(
                child: Text(
                  'Select your shopping language, region, and currency to personalize your storefront.',
                  style: TextStyle(fontSize: 11, color: Colors.grey, height: 1.5),
                  textAlign: TextAlign.center,
                ),
              ),
              const SizedBox(height: 40),

              // Language selector card
              _buildConfigOption(
                title: 'SELECT LANGUAGE',
                icon: Icons.language,
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<String>(
                    value: _selectedLanguage,
                    isExpanded: true,
                    style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.black, fontSize: 13),
                    items: _languages.map((String val) {
                      return DropdownMenuItem<String>(value: val, child: Text(val));
                    }).toList(),
                    onChanged: (val) {
                      if (val != null) setState(() => _selectedLanguage = val);
                    },
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Country selector card
              _buildConfigOption(
                title: 'SELECT COUNTRY',
                icon: Icons.place_outlined,
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<String>(
                    value: _selectedCountry,
                    isExpanded: true,
                    style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.black, fontSize: 13),
                    items: _countries.map((String val) {
                      return DropdownMenuItem<String>(value: val, child: Text(val));
                    }).toList(),
                    onChanged: (val) {
                      if (val != null) {
                        setState(() {
                          _selectedCountry = val;
                          _selectedCurrency = _currencies[val] ?? 'USD';
                        });
                      }
                    },
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Currency selector card
              _buildConfigOption(
                title: 'SELECT CURRENCY',
                icon: Icons.payments_outlined,
                child: Padding(
                  padding: const EdgeInsets.symmetric(vertical: 12.0),
                  child: Text(
                    _selectedCurrency,
                    style: const TextStyle(fontWeight: FontWeight.w900, color: Colors.black87, fontSize: 13),
                  ),
                ),
              ),
              const Spacer(),

              ElevatedButton(
                onPressed: _saveConfigs,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primaryColor,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  elevation: 0,
                ),
                child: const Text(
                  'CONFIRM PREFERENCES',
                  style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1.2),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildConfigOption({required String title, required IconData icon, required Widget child}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, size: 16, color: AppTheme.primaryColor),
              const SizedBox(width: 8),
              Text(
                title,
                style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 0.5),
              ),
            ],
          ),
          const SizedBox(height: 4),
          child,
        ],
      ),
    );
  }
}
