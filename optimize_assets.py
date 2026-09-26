import os
from PIL import Image

assets_dir = r"d:\BDA-shooting-championship\site\assets"

def optimize_image(filename, max_size, webp_quality=88):
    filepath = os.path.join(assets_dir, filename)
    if not os.path.exists(filepath):
        print(f"Skipping {filename}: not found")
        return
    
    name, ext = os.path.splitext(filename)
    with Image.open(filepath) as im:
        # RGBA check
        if im.mode != 'RGBA':
            im = im.convert('RGBA')
            
        orig_size = os.path.getsize(filepath)
        w, h = im.size
        
        # 1. Resize to max_size (e.g. 800x800) maintaining aspect ratio
        if w > max_size or h > max_size:
            ratio = min(max_size / w, max_size / h)
            new_size = (int(w * ratio), int(h * ratio))
            im_resized = im.resize(new_size, Image.Resampling.LANCZOS)
        else:
            im_resized = im.copy()
            
        # 2. Save optimized WebP
        webp_path = os.path.join(assets_dir, f"{name}.webp")
        im_resized.save(webp_path, "WEBP", quality=webp_quality, method=5)
        webp_size = os.path.getsize(webp_path)
        
        # 3. Save small mobile WebP (400x400)
        im_sm = im.resize((400, 400), Image.Resampling.LANCZOS)
        webp_sm_path = os.path.join(assets_dir, f"{name}-sm.webp")
        im_sm.save(webp_sm_path, "WEBP", quality=webp_quality, method=5)
        webp_sm_size = os.path.getsize(webp_sm_path)
        
        # 4. Overwrite original PNG with optimized 800x800 PNG
        png_path = filepath
        im_resized.save(png_path, "PNG", optimize=True)
        new_png_size = os.path.getsize(png_path)
        
        print(f"[{filename}] Orig: {orig_size/1024:.1f}KB -> Opt PNG: {new_png_size/1024:.1f}KB | WebP: {webp_size/1024:.1f}KB | Small WebP: {webp_sm_size/1024:.1f}KB")

print("Optimizing key site image assets for ultra-fast mobile loading...")
optimize_image("logo-bda.png", 800, webp_quality=88)
optimize_image("logo-championship.png", 800, webp_quality=88)
optimize_image("logo-championship-transparent.png", 800, webp_quality=88)

# Favicon optimization
fav_path = os.path.join(assets_dir, "favicon-bsc.png")
if os.path.exists(fav_path):
    orig = os.path.getsize(fav_path)
    with Image.open(fav_path) as im:
        im_192 = im.resize((192, 192), Image.Resampling.LANCZOS)
        im_192.save(fav_path, "PNG", optimize=True)
    print(f"[favicon-bsc.png] Orig: {orig/1024:.1f}KB -> {os.path.getsize(fav_path)/1024:.1f}KB")

print("Done asset optimization!")
