import os
import re

wp_classes = ['WP_Post', 'WP_Query', 'WP_Error', 'WP_User', 'WP_REST_Request', 'WP_REST_Response', 'WP_Screen', 'wpdb', 'WP_Theme', 'WP_Term', 'WP_Comment']

unqualified = []
for top in ['hackersshikkhok-core', 'hackersshikkhok-theme']:
    if not os.path.exists(top):
        continue
    for root, dirs, files in os.walk(top):
        for f in files:
            if not f.endswith('.php'):
                continue
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8', errors='ignore') as fp:
                content = fp.read()
            
            ns_match = re.search(r'namespace\s+([A-Za-z0-9_\\]+);', content)
            if not ns_match:
                continue
            
            uses = set(re.findall(r'use\s+([A-Za-z0-9_\\]+)(?:\s+as\s+[A-Za-z0-9_]+)?;', content))
            used_short_names = {u.split('\\')[-1] for u in uses}
            
            sig_matches = re.finditer(r'function\s+([a-zA-Z0-9_]+)\s*\(([^)]*)\)', content)
            for sm in sig_matches:
                fn_name = sm.group(1)
                params = sm.group(2)
                for cls in wp_classes:
                    if re.search(r'(?<![\\a-zA-Z0-9_])\??' + cls + r'\s+\$', params):
                        if cls not in used_short_names:
                            unqualified.append((path, fn_name, cls, params))

print(f"Total unqualified WP class type hints found in filesystem: {len(unqualified)}")
for item in unqualified:
    print(item)
