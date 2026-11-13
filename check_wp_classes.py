import os, re

wp_classes = ['WP_Post', 'WP_Query', 'WP_Error', 'WP_User', 'WP_REST_Request', 'WP_REST_Response', 'WP_Screen', 'wpdb', 'WP_Theme', 'WP_Term', 'WP_Comment']

found = []
for top in ['hackersshikkhok-core', 'hackersshikkhok-theme']:
    for root, dirs, files in os.walk(top):
        for f in files:
            if f.endswith('.php'):
                p = os.path.join(root, f)
                with open(p, 'r', errors='ignore') as fl:
                    lines = fl.readlines()
                has_ns = any(line.strip().startswith('namespace ') for line in lines)
                if not has_ns:
                    continue
                # check imports
                imported = set()
                for line in lines:
                    m = re.match(r'^\s*use\s+([^;]+);', line)
                    if m:
                        imported.add(m.group(1).strip().split('\\')[-1])
                for idx, line in enumerate(lines, 1):
                    for cls in wp_classes:
                        if cls in line and cls not in imported:
                            # if it has \WP_..., it's qualified
                            if ('\\' + cls) not in line:
                                # Check if used in function or type hint
                                if 'function ' in line or re.search(r'\b' + cls + r'\b', line):
                                    found.append((p, idx, cls, line.strip()))

print(f"Total occurrences: {len(found)}")
for p, idx, cls, l in found[:50]:
    print(f"{p}:{idx} [{cls}] -> {l}")
