#!/usr/bin/env python3
"""Build inc/data/murtadd-counterparts.json from murtadd's seed data.

murtadd.org rebuttals and doubts name their Compelling Evidence counterpart in
a `ce_slug` field (inc/data/seed/rebuttals.json and doubts.json in the murtadd
theme). This inverts that mapping: each CE article slug lists the murtadd
entries that point at it, so the article can link back.

murtadd permalinks come from its CPT registration: murtadd_rebuttal rewrites
to /rebuttals/<slug>/ (inc/cpt-rebuttal.php), murtadd_doubt to /doubts/<slug>/
(inc/cpt-doubt.php).

Usage: python3 bin/build-murtadd-counterparts.py [path/to/murtadd]
Default murtadd path: /home/user/murtadd. Slugs that do not exist in
inc/articles/batch-*.json are reported and left out.
"""

import glob
import json
import os
import sys

BASE = "https://murtadd.org"
TYPES = (("rebuttals", "rebuttal"), ("doubts", "doubt"))

ce_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
murtadd = sys.argv[1] if len(sys.argv) > 1 else "/home/user/murtadd"

articles = set()
for path in sorted(glob.glob(os.path.join(ce_root, "inc", "articles", "batch-*.json"))):
    with open(path, encoding="utf-8") as f:
        data = json.load(f)
    if isinstance(data, dict):
        data = data.get("articles", [])
    articles.update(a["slug"] for a in data if a.get("slug"))

out = {}
missing = []
for archive, kind in TYPES:
    with open(os.path.join(murtadd, "inc", "data", "seed", archive + ".json"), encoding="utf-8") as f:
        entries = json.load(f)
    for e in entries:
        ce = (e.get("ce_slug") or "").strip()
        if not ce:
            continue
        if ce not in articles:
            missing.append("%s/%s -> %s" % (archive, e["slug"], ce))
            continue
        out.setdefault(ce, []).append({
            "type": kind,
            "title": e["title"],
            "url": "%s/%s/%s/" % (BASE, archive, e["slug"]),
        })

out = {k: out[k] for k in sorted(out)}
dest = os.path.join(ce_root, "inc", "data", "murtadd-counterparts.json")
with open(dest, "w", encoding="utf-8") as f:
    json.dump(out, f, ensure_ascii=False, indent=2)
    f.write("\n")

print("%d articles, %d links -> %s" % (len(out), sum(len(v) for v in out.values()), dest))
for m in missing:
    print("missing CE article: " + m, file=sys.stderr)
sys.exit(1 if missing else 0)
