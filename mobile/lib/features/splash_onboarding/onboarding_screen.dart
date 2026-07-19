import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/storage/hive_storage.dart';

class OnboardingScreen extends StatefulWidget {
  const OnboardingScreen({super.key});

  @override
  State<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends State<OnboardingScreen> {
  final PageController _pageController = PageController();
  int _currentIndex = 0;

  final List<Map<String, String>> _slides = [
    {
      'title': 'DISCOVER THE LATEST TRENDS',
      'subtitle': 'Explore curated designer wear, ethnic collections, and western outfits tailormade for you.',
      'image': 'https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=600&auto=format&fit=crop'
    },
    {
      'title': 'EXCLUSIVE INSIDER REWARDS',
      'subtitle': 'Join the Aura Insider loyalty program, accumulate shopping points, and exchange them for coupons.',
      'image': 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=600&auto=format&fit=crop'
    },
    {
      'title': 'FAST & SECURE CHECKOUT',
      'subtitle': 'Quick deliveries, multiple online wallets, credit cards, and cash on delivery payments.',
      'image': 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=600&auto=format&fit=crop'
    }
  ];

  void _finishOnboarding() {
    HiveStorage.settings.put('onboarding_done', true);
    context.go('/login');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          PageView.builder(
            controller: _pageController,
            onPageChanged: (idx) => setState(() => _currentIndex = idx),
            itemCount: _slides.length,
            itemBuilder: (context, idx) {
              final slide = _slides[idx];
              return Column(
                children: [
                  Expanded(
                    flex: 6,
                    child: Container(
                      width: double.infinity,
                      decoration: BoxDecoration(
                        image: DecorationImage(
                          image: NetworkImage(slide['image']!),
                          fit: BoxFit.cover,
                        ),
                      ),
                    ),
                  ),
                  Expanded(
                    flex: 4,
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 30),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            children: [
                              Text(
                                slide['title']!,
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.w900,
                                  letterSpacing: 1.5,
                                  color: Colors.black,
                                ),
                                textAlign: Alignment.centerLeft,
                              ),
                              const SizedBox(height: 12),
                              Text(
                                slide['subtitle']!,
                                style: const TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                  height: 1.6,
                                  color: Colors.grey,
                                ),
                              ),
                            ],
                          ),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              // Dot Indicators
                              Row(
                                children: List.generate(
                                  _slides.length,
                                  (indicatorIdx) => Container(
                                    margin: const EdgeInsets.only(right: 6),
                                    width: _currentIndex == indicatorIdx ? 18 : 6,
                                    height: 6,
                                    decoration: BoxDecoration(
                                      color: _currentIndex == indicatorIdx
                                          ? const Color(0xFFFF3F6C)
                                          : Colors.grey.shade300,
                                      borderRadius: BorderRadius.circular(3),
                                    ),
                                  ),
                                ),
                              ),
                              // Navigation Button
                              ElevatedButton(
                                onPressed: () {
                                  if (_currentIndex < _slides.length - 1) {
                                    _pageController.nextPage(
                                      duration: const Duration(milliseconds: 300),
                                      curve: Curves.easeInOut,
                                    );
                                  } else {
                                    _finishOnboarding();
                                  }
                                },
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: const Color(0xFFFF3F6C),
                                  foregroundColor: Colors.white,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                                ),
                                child: Text(
                                  _currentIndex == _slides.length - 1 ? 'GET STARTED' : 'NEXT',
                                  style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900),
                                ),
                              ),
                            ],
                          )
                        ],
                      ),
                    ),
                  )
                ],
              );
            },
          ),
          
          // Skip Button top right
          Positioned(
            top: MediaQuery.of(context).padding.top + 16,
            right: 16,
            child: TextButton(
              onPressed: _finishOnboarding,
              child: const Text(
                'SKIP',
                style: TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.w900,
                  fontSize: 11,
                  shadows: [Shadow(color: Colors.black45, blurRadius: 4)],
                ),
              ),
            ),
          )
        ],
      ),
    );
  }
}
