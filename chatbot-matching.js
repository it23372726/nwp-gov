/**
 * NWPC Chatbot advanced intent-matching engine (browser + Node).
 * Hybrid: local fuzzy scoring with confidence gate; LLM fallback via match_intent.php.
 */
(function (root, factory) {
    const api = factory();
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = api;
    }
    root.NWPCMatching = api;
})(typeof globalThis !== 'undefined' ? globalThis : this, function () {
    'use strict';

    const HIGH_THRESHOLD = 0.72;
    const MARGIN = 0.08;
    const SOFT_THRESHOLD = 0.48;
    const MATCH_THRESHOLD = 0.42;
    const NGRAM_SIZE = 3;

    const INSTITUTION_PREFIXES = [
        'agri_', 'isb_', 'wda_', 'probation_', 'health_ministry_', 'irrigation_', 'rural_dev_',
        'moe_ministry_', 'housing_', 'eng_services_', 'pc_secretariat_', 'hrda_', 'rthgr_ministry_', 'revenue_',
    ];

    const INSTITUTION_SIGNAL_SETS = {
        agri: [
            'agriculture', 'agricultural', 'farming', 'farmer', 'crop', 'soil', 'seed',
            'gap', 'pesticide', 'paddy', 'farm',
            'කෘෂි', 'ගොවි', 'පස්', 'බීජ', 'වගා',
            'விவசாய', 'பண்ணை', 'மண்', 'விதை', 'பயிர்',
        ],
        isb: [
            'isb', 'industrial services bureau', 'industrial services', 'consultancy',
            'අයිඑස්බී', 'කර්මාන්ත සේවා',
            'ஐஎஸ்பி', 'தொழில் சேவை',
        ],
        wda: [
            'wda', 'wayamba development authority',
            'tourism', 'tourist', 'hotel school', 'boyagane', 'mangul oya',
            'investment promotion', 'cat 20', 'badagamuwa', 'puttalam bungalow',
            'වයඹ සංවර්ධන අධිකාරිය', 'සංචාරක', 'ආයෝජන', 'හෝටල් පාසල', 'බෝයගනේ',
            'வயம்பா அபிவிருத்தி', 'சுற்றுலா', 'முதலீடு', 'வடமேற்கு அபிவிருத்தி அதிகாரம்',
        ],
        probation: [
            'probation', 'child care', 'childcare', 'child care services', 'adoption',
            'children home', 'childrens home', 'daycare', 'day care', 'foster care',
            'juvenile', 'probation officer', 'twins assistance',
            'පරිවාස', 'ළමාරක්ෂක', 'දරුකමට', 'ළමා නිවාස', 'දිවා සුරැකුම්', 'ළමා ආරක්ෂාව',
            'பரோல்', 'குழந்தை பராமரிப்பு', 'தத்தெடுத்தல்', 'குழந்தை பாதுகாப்பு', 'பராமரிப்பு',
        ],
        health_ministry: [
            'health ministry', 'ministry of health', 'provincial health', 'social welfare',
            'women affairs', 'parliamentary affairs', 'indigenous medicine', 'ayurveda department',
            'healthmin', 'citizen charter health', 'women society registration', 'rti health ministry',
            'සෞඛ්‍ය අමාත්‍යාංශ', 'දේශීය වෛද්‍ය', 'සමාජ සුබසාධන', 'කාන්තා කටයුතු',
            'සභා කටයුතු', 'වනිතා සමිති', 'පළාත් සෞඛ්‍ය',
            'சுகாதார அமைச்சு', 'சமூக நலன்', 'பெண்கள் விவகாரங்கள்', 'ஆயுர்வேத',
        ],
        irrigation: [
            'irrigation', 'irrigation department', 'irridept', 'irrigation citizen charter',
            'emergency irrigation', 'irrigation canal', 'irrigation maintenance',
            'වාරිමාර්ග', 'වාරි මාර්ග', 'හදිසි වාරි', 'වාරිමාර්ග දෙපාර්තමේන්තුව',
            'நீர்ப்பாசன', 'நீர்ப்பாசனத் திணைக்களம்', 'பாசனம்',
        ],
        rural_dev: [
            'rural development', 'rural dev department', 'grama sanvardhana', 'rural society registration',
            'women rural development society', 'rural development wayamba',
            'ග්‍රාම සංවර්ධන', 'ග්‍රාම සංවර්ධන දෙපාර්තමේන්තුව', 'ග්‍රාම සංවර්ධන සමිති',
            'කාන්තා ග්‍රාම සංවර්ධන', 'සමිති ලියාපදිංචි ග්‍රාම', 'මුදල් නිදහස් කිරීම සමිති',
            'ஊரக வளர்ச்சி', 'கிராம அபிவிருத்தி', 'கிராம சங்க பதிவு',
        ],
        moe_ministry: [
            'moe ministry', 'moe.nw.gov', 'nwpmoe', 'provincial lands ministry',
            'cooperative development ministry', 'sports youth ministry wayamba',
            'cultural affairs ministry', 'lands department wayamba ministry',
            'සමූපකාර සංවර්ධන අමාත්‍යංශ', 'පළාත් සමූපකාර අමාත්‍යංශ',
            'ඉඩම් අංශය', 'සමූපකාර අංශය', 'ක්‍රීඩා අංශය', 'සංස්කෘතික අංශය',
            'පළාත් ඉඩම් කොමසාරිස්', 'ක්‍රීඩා දීමනා', 'කලා උළෙල වයඹ',
            'கூட்டுறவு அமைச்சு', 'விளையாட்டு அமைச்சு', 'நில அமைச்சு',
        ],
        housing: [
            'housing department', 'housing construction wayamba', 'housedep', 'housing citizen charter',
            'sirpiyasa housing', 'wayamba sarana', 'housing loan wayamba', 'rent control board',
            'green city housing', 'land plot housing',
            'නිවාස හා ඉදිකිරීම්', 'නිවාස දෙපාර්තමේන්තුව', 'සිරිපියස', 'වයඹ සරණ',
            'නිවාස ආධාර', 'ගෙවල් කුලී', 'තිරසර පුරවර', 'නිවාස ණය',
            'வீட்டுவசதி', 'வீட்டுவசதி திணைக்களம்', 'வீட்டுக் கடன்',
        ],
        eng_services: [
            'chief secretary engineering', 'engineering services division', 'cs.nw.gov', 'dcs eng nwp',
            'deputy chief secretary engineering', 'contractor registration wayamba', 'quality control center wayamba',
            'technical officer placement', 'construction material approval',
            'ප්‍රධාන ලේකම් ඉංජිනේරු', 'ඉංජිනේරු සේවා අංශය', 'නි.ප්‍ර.ලේ. ඉංජි.සේවා',
            'කොන්ත්‍රත් ආයතන ලියාපදිංචි', 'තත්ත්ව පාලන මධ්‍යස්ථානය', 'ඉදිකිරීම් ද්‍රව්‍ය අනුමැතිය',
            'பிரதம செயலாளர் பொறியியல்', 'பொறியியல் சேவை பிரிவு',
        ],
        pc_secretariat: [
            'provincial council secretariat', 'pc secretariat', 'pcsec.nw.gov', 'pcsec wayamba',
            'provincial council services', 'standing orders wayamba pc',
            'පළාත් සභා ලේකම්', 'සභා ලේකම් කාර්යාලය', 'මහජන යෝජනා පළාත් සභා',
            'ප්‍රඥප්තිය පළාත් සභා', 'මහජන පෙත්සම්', 'තොරතුරු දැනගැනීමේ පනත පළාත් සභා',
            'மாகாண சபை செயலகம்', 'மாகாண சபை சேவைகள்',
        ],
        hrda: [
            'human resources development authority', 'hr development authority', 'hrda.nw.gov', 'hrdanwp',
            'mahagedara wayamba', 'mahagedara.nw.gov', 'revolving loan wayamba', 'rural organization loan',
            'මානව සම්පත් සංවර්ධන', 'මහගෙදර කුරුණෑගල', 'චක්‍රීය ණය යෝජනා', 'පුහුණු ආයතන ලියාපදිංචි',
            'ආරක්ෂක නියාමක බඳවා ගැනීම', 'මනෝ විද්‍යා උපදේශන වයඹ',
            'மனித வள அபிவிருத்தி', 'மககெதரை', 'மனித வள அதிகாரசபை',
        ],
        rthgr_ministry: [
            'roads transport housing ministry', 'rthgr ministry', 'roadmin.nw.gov',
            'ministry roads transport housing rural', 'provincial ministry roads wayamba',
            'මාර්ග ප්‍රවාහන නිවාස අමාත්‍යංශය', 'ඉදිකිරීම් කර්මාන්ත ග්‍රාම අමාත්‍යංශය',
            'මාර්ගස්ථ මගී ප්‍රවාහන අමාත්‍යංශය', 'ප්‍රායෝගික පුහුණුවන්නන් අමාත්‍යංශය',
            'சாலை போக்குவரத்து வீட்டுவசதி அமைச்சு', 'வீதி அமைச்சகம் வயம்பா',
        ],
        revenue: [
            'provincial revenue department', 'revenue department wayamba', 'prorevdept.nw.gov',
            'stamp duty wayamba', 'sadana pathra', 'property valuation opinion',
            'වයඹ පළාත් ආදායම්', 'ආදායම් දෙපාර්තමේන්තුව', 'සාධන පත්‍ර',
            'මුද්දර ගාස්තු', 'තක්සේරු අභියාචනය', 'මත සහතික කිරීම',
            'வருமானத் திணைக்களம்', 'முத்திரை வரி', 'வயம்பா வருமானம்',
        ],
    };

    // Pre-normalize institution signals once
    const NORMALIZED_SIGNALS = {};
    Object.keys(INSTITUTION_SIGNAL_SETS).forEach((key) => {
        NORMALIZED_SIGNALS[key] = INSTITUTION_SIGNAL_SETS[key]
            .map((t) => normalizeStatic(t))
            .filter(Boolean);
    });

    const COMPLAINT_TERMS = [
        'complaint', 'complain', 'grievance', 'feedback', 'raise an issue', 'report to',
        'problem', 'issue with', 'ගැටලු', 'ගැටළු', 'පැමිණිලි', 'පැමිණිල්ල', 'புகார்',
    ].map(normalizeStatic);

    const SERVICE_TIME_TERMS = [
        'service times', 'service time', 'service standards', 'processing time', 'turnaround',
        'service duration', 'standards',
        'සේවා කාලය', 'සේවා ප්‍රමිතීන්', 'කාලසටහන', 'சேவை நேரம்', 'சேவை தரநிலை',
    ].map(normalizeStatic);

    function normalizeStatic(text) {
        let s = String(text || '');
        // NFC keeps Sinhala/Tamil conjuncts intact (unlike stripping marks)
        if (typeof s.normalize === 'function') s = s.normalize('NFC');
        s = s.toLowerCase();
        // Keep ZWJ/ZWNJ (needed for Sinhala conjuncts like ඛ්‍ය); drop only BOM / soft hyphen / ZWSP
        s = s.replace(/[\u200B\uFEFF\u00AD]/g, '');
        // Keep letters, numbers, marks (virama), and ZWJ/ZWNJ
        s = s.replace(/[^\p{L}\p{N}\p{M}\u200C\u200D\s]/gu, ' ');
        return s.replace(/\s+/g, ' ').trim();
    }

    function normalize(text) {
        return normalizeStatic(text);
    }

    function tokenize(text) {
        return normalize(text).split(/\s+/).filter((w) => w.length > 1);
    }

    function charNgrams(compact, n) {
        if (compact.length < n) return compact.length ? [compact] : [];
        const grams = [];
        for (let i = 0; i <= compact.length - n; i++) {
            grams.push(compact.slice(i, i + n));
        }
        return grams;
    }

    function jaccardFromSets(setA, setB, sizeA, sizeB) {
        if (!sizeA || !sizeB) return 0;
        let inter = 0;
        setA.forEach((x) => {
            if (setB.has(x)) inter++;
        });
        const union = sizeA + sizeB - inter;
        return union === 0 ? 0 : inter / union;
    }

    function levenshtein(a, b) {
        if (a === b) return 0;
        if (!a.length) return b.length;
        if (!b.length) return a.length;
        // Prefer shorter as columns
        if (a.length > b.length) {
            const tmp = a;
            a = b;
            b = tmp;
        }
        const m = a.length;
        const n = b.length;
        let prev = new Array(m + 1);
        let curr = new Array(m + 1);
        for (let i = 0; i <= m; i++) prev[i] = i;
        for (let j = 1; j <= n; j++) {
            curr[0] = j;
            const bj = b[j - 1];
            for (let i = 1; i <= m; i++) {
                const cost = a[i - 1] === bj ? 0 : 1;
                curr[i] = Math.min(prev[i] + 1, curr[i - 1] + 1, prev[i - 1] + cost);
            }
            const swap = prev;
            prev = curr;
            curr = swap;
        }
        return prev[m];
    }

    function wordSimilarity(w1, w2) {
        if (!w1 || !w2) return 0;
        if (w1 === w2) return 1;
        const len = Math.max(w1.length, w2.length);
        if (!len) return 0;
        // Length gate: skip expensive DP when lengths differ too much
        if (Math.abs(w1.length - w2.length) / len > 0.45) return 0;
        return Math.max(0, 1 - levenshtein(w1, w2) / len);
    }

    function isIndicScript(w) {
        return /[\u0D80-\u0DFF\u0B80-\u0BFF]/.test(w);
    }

    function adaptiveWordSim(w1, w2) {
        if (!w1 || !w2) return 0;
        if (w1 === w2) return 1;
        const len = Math.max(w1.length, w2.length);
        const minLen = Math.min(w1.length, w2.length);
        if (Math.abs(w1.length - w2.length) > Math.max(2, Math.floor(len * 0.35))) return 0;
        const dist = levenshtein(w1, w2);
        const sim = Math.max(0, 1 - dist / len);

        // Sinhala/Tamil: do not treat near-misses like සේවය≈සේවා as equal
        if (isIndicScript(w1) && isIndicScript(w2)) {
            if (dist === 0) return 1;
            if (minLen <= 5) return sim >= 0.92 ? sim : 0;
            return sim >= 0.82 ? sim : 0;
        }

        // Latin: allow 1-edit typos (tst/test, helth/health)
        if (minLen >= 3 && dist === 1) return Math.max(sim, 0.84);
        if (minLen >= 6 && dist === 2 && sim >= 0.7) return Math.max(sim, 0.78);
        if (minLen <= 3) return sim >= 0.9 ? sim : 0;
        if (minLen <= 5) return sim >= 0.75 ? sim : 0;
        return sim >= 0.62 ? sim : 0;
    }

    function tokenSetScore(userTokens, keyTokens) {
        if (!userTokens.length || !keyTokens.length) return 0;
        let matched = 0;
        let totalSim = 0;
        for (let i = 0; i < keyTokens.length; i++) {
            const kw = keyTokens[i];
            let best = 0;
            for (let j = 0; j < userTokens.length; j++) {
                const s = adaptiveWordSim(userTokens[j], kw);
                if (s > best) best = s;
                if (best >= 0.99) break;
            }
            if (best >= 0.62) {
                matched++;
                totalSim += best;
            }
        }
        const coverage = matched / keyTokens.length;
        const avgSim = matched ? totalSim / matched : 0;
        return coverage * 0.65 + avgSim * 0.35;
    }

    function phraseSimilarity(cleanInput, keyNorm, cache) {
        if (!cleanInput || !keyNorm) return 0;
        if (cleanInput === keyNorm) return 1;

        // Avoid short keyword substring false positives
        if (keyNorm.length >= 4 && cleanInput.includes(keyNorm)) {
            return 0.92 + Math.min(0.08, (keyNorm.length / Math.max(cleanInput.length, 1)) * 0.08);
        }
        if (keyNorm.length <= 3 && cleanInput.split(/\s+/).includes(keyNorm)) {
            return 0.9;
        }
        if (keyNorm.includes(cleanInput) && cleanInput.length >= keyNorm.length * 0.65 && cleanInput.length >= 4) {
            return 0.88;
        }
        if (keyNorm.includes(cleanInput) && cleanInput.length >= 6) {
            return 0.55;
        }

        const userTokens = cache.userTokens;
        const keyTokens = keyNorm.split(/\s+/).filter((w) => w.length > 1);
        const tokenScore = tokenSetScore(userTokens, keyTokens);

        const keyCompact = keyNorm.replace(/\s+/g, '');
        // Skip n-gram for very short keys (noisy)
        let ngramScore = 0;
        if (keyCompact.length >= 5 && cache.compact.length >= 5) {
            const keyGrams = charNgrams(keyCompact, NGRAM_SIZE);
            const keySet = new Set(keyGrams);
            ngramScore = jaccardFromSets(cache.gramSet, keySet, cache.gramSet.size, keySet.size);
        }

        return Math.max(tokenScore, ngramScore * 0.95, tokenScore * 0.55 + ngramScore * 0.45);
    }

    function scoreKeyword(cleanInput, keyword, cache) {
        const keyNorm = normalize(keyword);
        if (!keyNorm) return 0;
        return phraseSimilarity(cleanInput, keyNorm, cache);
    }

    function tokenBoundaryIncludes(clean, signal) {
        if (!signal) return false;
        if (signal.length <= 3) {
            // Short signals: whole-token match only (avoid "gap" inside random text)
            return clean.split(/\s+/).includes(signal);
        }
        return clean.includes(signal);
    }

    function signalMatches(clean, userTokens, signal) {
        if (!signal) return false;
        if (tokenBoundaryIncludes(clean, signal)) return true;
        const parts = signal.split(/\s+/).filter((w) => w.length > 1);
        if (parts.length === 1) {
            const s = parts[0];
            // Fuzzy only for longer tokens
            if (s.length < 5) return false;
            for (let i = 0; i < userTokens.length; i++) {
                if (adaptiveWordSim(userTokens[i], s) >= 0.8) return true;
            }
            return false;
        }
        let matched = 0;
        for (let i = 0; i < parts.length; i++) {
            const p = parts[i];
            let ok = tokenBoundaryIncludes(clean, p);
            if (!ok && p.length >= 5) {
                for (let j = 0; j < userTokens.length; j++) {
                    if (adaptiveWordSim(userTokens[j], p) >= 0.8) {
                        ok = true;
                        break;
                    }
                }
            }
            if (ok) matched++;
        }
        return matched >= Math.ceil(parts.length * 0.75);
    }

    function detectInstitutionContext(cleanInput) {
        const clean = normalize(cleanInput);
        const userTokens = tokenize(clean);
        const flags = [];
        Object.keys(NORMALIZED_SIGNALS).forEach((key) => {
            const hit = NORMALIZED_SIGNALS[key].some((term) => signalMatches(clean, userTokens, term));
            if (hit) flags.push(key);
        });
        return { flags, single: flags.length === 1 ? flags[0] : null };
    }

    function getInstitutionAffinity(single, itemId) {
        if (!single) return 0;
        const prefix = single + '_';
        if (itemId.startsWith(prefix)) return 0.12;
        if (INSTITUTION_PREFIXES.some((p) => itemId.startsWith(p))) return -0.1;
        return 0;
    }

    function hasAnyTerm(clean, userTokens, terms) {
        for (let i = 0; i < terms.length; i++) {
            const t = terms[i];
            if (!t) continue;
            if (clean.includes(t) || clean === t) return true;
            if (t.length >= 4 && userTokens.some((u) => adaptiveWordSim(u, t) >= 0.85)) return true;
        }
        return false;
    }

    function getComplaintAffinity(single, clean, userTokens, itemId) {
        if (!single || !hasAnyTerm(clean, userTokens, COMPLAINT_TERMS)) return 0;
        if (itemId.startsWith(single + '_') && itemId.includes('complaint')) return 0.14;
        if (itemId.startsWith(single + '_') && (itemId.includes('contact') || itemId.includes('officers'))) {
            return 0.08;
        }
        return 0;
    }

    function getServiceTimesAffinity(single, clean, userTokens, itemId) {
        if (!single || !hasAnyTerm(clean, userTokens, SERVICE_TIME_TERMS)) return 0;
        if (itemId.startsWith(single + '_') && (itemId.endsWith('_service_times') || itemId.endsWith('_service_standards'))) {
            return 0.2;
        }
        if (itemId.startsWith(single + '_') && itemId.includes('citizen_charter_services')) return -0.08;
        if (itemId.startsWith(single + '_') && itemId.endsWith('_services')) return -0.06;
        return 0;
    }

    function isGenericComplaintOnly(clean) {
        return COMPLAINT_TERMS.some((term) => term === clean);
    }

    function isAboutEntry(id) {
        return id.endsWith('_about');
    }

    function userTokenCoverage(userTokens, item) {
        if (!userTokens.length) return 0;
        let hit = 0;
        const keywords = item.keywords || [];
        for (let i = 0; i < userTokens.length; i++) {
            const uw = userTokens[i];
            let ok = false;
            for (let k = 0; k < keywords.length && !ok; k++) {
                const parts = normalize(keywords[k]).split(/\s+/).filter((w) => w.length > 1);
                for (let p = 0; p < parts.length; p++) {
                    if (adaptiveWordSim(uw, parts[p]) >= 0.8) {
                        ok = true;
                        break;
                    }
                }
            }
            if (ok) hit++;
        }
        return hit / userTokens.length;
    }

    function hasExactTerm(clean, userTokens, terms) {
        for (let i = 0; i < terms.length; i++) {
            const t = terms[i];
            if (!t) continue;
            if (clean === t || clean.includes(t)) return true;
            if (userTokens.includes(t)) return true;
        }
        return false;
    }

    function scoreEntry(clean, cache, item, single) {
        let bestKw = 0;
        let bestKwLen = 0;
        const keywords = item.keywords || [];
        for (let i = 0; i < keywords.length; i++) {
            const keyNorm = normalize(keywords[i]);
            if (!keyNorm) continue;
            const s = phraseSimilarity(clean, keyNorm, cache);
            if (s > bestKw || (s === bestKw && keyNorm.length > bestKwLen)) {
                bestKw = s;
                bestKwLen = keyNorm.length;
            }
            if (bestKw >= 0.99 && bestKwLen >= 12) break;
        }

        let score = bestKw;
        if (bestKw >= 0.85 && bestKwLen >= 10) {
            score += Math.min(0.1, bestKwLen / 80);
        }

        score += getInstitutionAffinity(single, item.id);
        score += getComplaintAffinity(single, clean, cache.userTokens, item.id);
        score += getServiceTimesAffinity(single, clean, cache.userTokens, item.id);
        score += userTokenCoverage(cache.userTokens, item) * 0.06;

        if (isAboutEntry(item.id) && keywords.some((kw) => normalize(kw) === clean)) {
            score += 0.18;
        }

        // Exact cues only (avoid සේවය ≈ සේවා false trigger)
        // Strip institution names that literally contain "services" so they don't look like a services ask.
        const servicesProbe = clean
            .replace(/industrial services bureau/g, 'isb')
            .replace(/industral services bureau/g, 'isb')
            .replace(/කර්මාන්ත සේවා කාර්යාංශය/g, 'isb')
            .replace(/කර්මාන්ත සේවා/g, 'isb')
            .replace(/தொழில் சேவைகள் பணியகம்/g, 'isb')
            .replace(/தொழில் சேவை பணியகம்/g, 'isb')
            .replace(/\s+/g, ' ')
            .trim();
        const servicesTokens = tokenize(servicesProbe);
        const asksServices = hasExactTerm(servicesProbe, servicesTokens, [
            'services', 'service', 'සේවා', 'சேவைகள்', 'சேவை',
        ].map(normalize));
        const asksAbout = hasExactTerm(clean, cache.userTokens, [
            'about', 'what is', 'tell me about', 'ගැන', 'என்ன',
        ].map(normalize));
        const asksContact = hasExactTerm(clean, cache.userTokens, [
            'contact', 'phone', 'call', 'telephone', 'email', 'address',
            'සම්බන්ධ', 'දුරකථන', 'தொடர்பு', 'தொலைபேசி',
        ].map(normalize));
        const asksComplaint = hasAnyTerm(clean, cache.userTokens, COMPLAINT_TERMS);

        if ((asksServices || asksContact || asksComplaint) && isAboutEntry(item.id)) {
            score -= 0.28;
        }
        if (asksAbout && isAboutEntry(item.id)) {
            score += 0.18;
        }
        if (asksAbout && item.id.endsWith('_services') && !item.id.includes('citizen_charter')) {
            score -= 0.12;
        }
        if (
            asksServices
            && item.id.endsWith('_services')
            && !item.id.includes('citizen_charter')
            && !item.id.includes('child_')
            && !item.id.includes('service_times')
            && !item.id.includes('service_standards')
        ) {
            score += 0.22;
        }
        if (asksContact && (item.id.includes('contact') || item.id.includes('officers'))) {
            score += 0.16;
        }
        if (asksComplaint && item.id.includes('complaint')) {
            score += 0.08;
        }

        return Math.max(0, Math.min(1.3, score));
    }

    function rankIntents(input, knowledgeBase) {
        const clean = normalize(input);
        if (clean.length < 2) {
            return { clean, ranked: [], userWords: [], single: null };
        }
        const userTokens = tokenize(clean);
        if (!userTokens.length) {
            return { clean, ranked: [], userWords: [], single: null };
        }

        const ctx = detectInstitutionContext(clean);
        if (isGenericComplaintOnly(clean) && !ctx.single) {
            return { clean, ranked: [], userWords: userTokens, single: null };
        }

        const compact = clean.replace(/\s+/g, '');
        const grams = charNgrams(compact, NGRAM_SIZE);
        const cache = {
            userTokens,
            compact,
            gramSet: new Set(grams),
        };

        const ranked = (knowledgeBase || []).map((item) => ({
            item,
            id: item.id,
            score: scoreEntry(clean, cache, item, ctx.single),
        }));

        ranked.sort((a, b) => {
            if (b.score !== a.score) return b.score - a.score;
            const aAbout = isAboutEntry(a.id);
            const bAbout = isAboutEntry(b.id);
            if (aAbout && !bAbout) return 1;
            if (!aAbout && bAbout) return -1;
            return a.id.localeCompare(b.id);
        });

        return { clean, ranked, userWords: userTokens, single: ctx.single };
    }

    function detectIntentDetailed(input, knowledgeBase, options) {
        const opts = options || {};
        const high = opts.highThreshold ?? HIGH_THRESHOLD;
        const margin = opts.margin ?? MARGIN;
        const soft = opts.softThreshold ?? SOFT_THRESHOLD;
        const matchThresh = opts.matchThreshold ?? MATCH_THRESHOLD;

        const { ranked } = rankIntents(input, knowledgeBase);
        const top = ranked[0] || null;
        const second = ranked[1] || null;
        const bestScore = top ? top.score : 0;
        const secondScore = second ? second.score : 0;
        const confident = Boolean(
            top && bestScore >= high && (bestScore - secondScore) >= margin
        );
        const softMatch = Boolean(top && bestScore >= soft);
        const intent = top && bestScore >= matchThresh ? top.item : null;

        return {
            intent: intent ? { ...intent, _matchScore: bestScore } : null,
            score: bestScore,
            secondScore,
            confident,
            softMatch,
            candidates: ranked.slice(0, 12).map((r) => ({
                id: r.id,
                score: Number(r.score.toFixed(4)),
            })),
        };
    }

    function detectIntent(input, knowledgeBase) {
        const detailed = detectIntentDetailed(input, knowledgeBase);
        return detailed.intent;
    }

    function scoreAllIntents(input, knowledgeBase) {
        const { ranked } = rankIntents(input, knowledgeBase);
        return ranked.map((r) => ({ id: r.id, score: r.score }));
    }

    function scoreKeywordLegacy(cleanInput, userWords, keyword) {
        const compact = cleanInput.replace(/\s+/g, '');
        const cache = {
            userTokens: userWords || tokenize(cleanInput),
            compact,
            gramSet: new Set(charNgrams(compact, NGRAM_SIZE)),
        };
        return scoreKeyword(cleanInput, keyword, cache) * 25;
    }

    return {
        normalize,
        scoreKeyword: scoreKeywordLegacy,
        wordSimilarity,
        phraseSimilarity: (a, b) => {
            const clean = normalize(a);
            const key = normalize(b);
            const compact = clean.replace(/\s+/g, '');
            return phraseSimilarity(clean, key, {
                userTokens: tokenize(clean),
                compact,
                gramSet: new Set(charNgrams(compact, NGRAM_SIZE)),
            });
        },
        detectInstitutionContext,
        getInstitutionAffinity: (clean, itemId) => {
            const { single } = detectInstitutionContext(clean);
            return getInstitutionAffinity(single, itemId);
        },
        detectIntent,
        detectIntentDetailed,
        scoreAllIntents,
        INSTITUTION_PREFIXES,
        HIGH_THRESHOLD,
        MARGIN,
        SOFT_THRESHOLD,
        MATCH_THRESHOLD,
    };
});
