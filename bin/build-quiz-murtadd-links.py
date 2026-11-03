#!/usr/bin/env python3
"""Build inc/data/quiz-murtadd-links.json for the quiz's "You are not alone" list.

The quiz result for the Questioning Muslim, the Ex-Believer and the Committed
Muslim links to two or three murtadd.org pages, chosen from the visitor's
answers (see murtaddPicks() in assets/js/quiz.js). PICKS below says which
pages go with which answer. This script looks every slug up in murtadd's seed
data and takes its title from there, so a renamed or deleted page fails the
build and never ships as a dead link.

murtadd permalinks come from its CPT registrations: murtadd_doubt rewrites to
/doubts/<slug>/ (inc/cpt-doubt.php), murtadd_guide to /guides/<slug>/
(inc/cpt-guide.php) and murtadd_rebuttal to /rebuttals/<slug>/
(inc/cpt-rebuttal.php). The output holds paths only; page-quiz.php adds the
base URL (filter ce_murtadd_base_url, default https://murtadd.org).

Usage: python3 bin/build-quiz-murtadd-links.py [path/to/murtadd]
Default murtadd path: /home/user/murtadd.
"""

import json
import os
import sys

ARCHIVES = ("doubts", "guides", "rebuttals")
PERSONAS = ["muslim-doubts", "ex-believer", "true-muslim"]

# Keys are the follow-up answers ("Is there one thing that weighs on you
# most?"), plus a default list per persona and one for the Committed Muslim.
PICKS = {
    "family": ["doubts/leaving-would-cost-me-my-family", "doubts/my-spouse-does-not-know"],
    "text": ["doubts/the-hadith-about-leaving", "doubts/verses-that-were-cancelled"],
    "law": ["doubts/what-the-law-says-in-malaysia", "guides/leaving-and-the-law"],
    "hurt": ["doubts/i-was-hurt-and-nobody-helped", "doubts/hurt-by-a-religious-teacher"],
    "prayer": ["doubts/i-stopped-praying-and-feel-guilty", "doubts/doubting-through-ramadan"],
    "online": ["doubts/websites-confirmed-my-doubts", "guides/is-islam-false"],
    "default-muslim-doubts": ["guides/is-islam-false", "doubts/websites-confirmed-my-doubts"],
    "default-ex-believer": ["doubts/i-studied-islam-and-still-left", "guides/leaving-and-the-law"],
    "true-muslim": ["guides/supporting-someone-who-doubts", "guides/is-islam-false"],
}

ce_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
murtadd = sys.argv[1] if len(sys.argv) > 1 else "/home/user/murtadd"

titles = {}
for archive in ARCHIVES:
    with open(os.path.join(murtadd, "inc", "data", "seed", archive + ".json"), encoding="utf-8") as f:
        for e in json.load(f):
            titles["%s/%s" % (archive, e["slug"])] = e["title"]

items, missing = {}, []
for key in sorted({k for v in PICKS.values() for k in v}):
    if key not in titles:
        missing.append(key)
        continue
    items[key] = {"title": titles[key], "path": "/%s/" % key}

if missing:
    sys.exit("Not in murtadd's seed data: " + ", ".join(missing))

out = {"personas": PERSONAS, "picks": PICKS, "items": items}
dest = os.path.join(ce_root, "inc", "data", "quiz-murtadd-links.json")
with open(dest, "w", encoding="utf-8") as f:
    json.dump(out, f, ensure_ascii=False, indent=2)
    f.write("\n")
print("Wrote %d murtadd pages to %s" % (len(items), os.path.relpath(dest, ce_root)))
