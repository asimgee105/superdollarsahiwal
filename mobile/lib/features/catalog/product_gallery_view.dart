import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class ProductGalleryView extends StatefulWidget {
  final List<String> images;
  const ProductGalleryView({super.key, required this.images});

  @override
  State<ProductGalleryView> createState() => _ProductGalleryViewState();
}

class _ProductGalleryViewState extends State<ProductGalleryView> {
  late PageController _pageController;
  int _currentIndex = 0;
  bool _isPlayingVideo = false;

  @override
  void initState() {
    super.initState();
    _pageController = PageController();
  }

  @override
  void dispose() {
    _pageController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // Fallback if images list is empty
    final list = widget.images.isNotEmpty 
      ? widget.images 
      : ['https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=600&auto=format&fit=crop'];

    return Scaffold(
      backgroundColor: Colors.black,
      body: Stack(
        children: [
          // Gallery view
          Positioned.fill(
            child: _isPlayingVideo
                ? Center(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(Icons.play_circle_fill, size: 72, color: Colors.white),
                        const SizedBox(height: 16),
                        const Text('PLAYING PRODUCT SHOWCASE VIDEO', style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.bold, letterSpacing: 0.5)),
                        const SizedBox(height: 16),
                        TextButton(
                          onPressed: () => setState(() => _isPlayingVideo = false),
                          child: const Text('PAUSE & SHOW IMAGES', style: TextStyle(color: Color(0xFFFF3F6C), fontWeight: FontWeight.w900, fontSize: 10)),
                        )
                      ],
                    ),
                  )
                : PageView.builder(
                    controller: _pageController,
                    onPageChanged: (idx) => setState(() => _currentIndex = idx),
                    itemCount: list.length,
                    itemBuilder: (context, index) {
                      return InteractiveViewer(
                        minScale: 0.5,
                        maxScale: 3.5,
                        child: Center(
                          child: Image.network(
                            list[index],
                            fit: BoxFit.contain,
                          ),
                        ),
                      );
                    },
                  ),
          ),

          // Top Header Bar
          Positioned(
            top: MediaQuery.of(context).padding.top + 10,
            left: 16,
            right: 16,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                IconButton(
                  icon: const Icon(Icons.close, color: Colors.white, size: 24),
                  onPressed: () => Navigator.pop(context),
                ),
                Text(
                  _isPlayingVideo ? 'VIDEO VIEW' : '${_currentIndex + 1} / ${list.length}',
                  style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 12),
                ),
                IconButton(
                  icon: Icon(_isPlayingVideo ? Icons.image_outlined : Icons.play_circle_outline, color: Colors.white, size: 24),
                  onPressed: () => setState(() => _isPlayingVideo = !_isPlayingVideo),
                ),
              ],
            ),
          ),

          // Bottom Slide Indicators
          if (!_isPlayingVideo)
            Positioned(
              bottom: 40,
              left: 0,
              right: 0,
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: List.generate(
                  list.length,
                  (indicatorIndex) => Container(
                    margin: const EdgeInsets.symmetric(horizontal: 4),
                    width: _currentIndex == indicatorIndex ? 18 : 6,
                    height: 6,
                    decoration: BoxDecoration(
                      color: _currentIndex == indicatorIndex ? const Color(0xFFFF3F6C) : Colors.white30,
                      borderRadius: BorderRadius.circular(3),
                    ),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }
}
