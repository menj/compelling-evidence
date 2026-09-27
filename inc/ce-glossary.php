<?php
/**
 * Glossary Auto-Linking System with Rollover Tooltips
 * Auto-links Islamic/Arabic terms in articles and shows definition tooltips
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get all glossary terms as a flat array for lookup
 */

/**
 * Get all glossary terms — single source of truth.
 *
 * Returns 93 terms with both short tooltips ('def') and rich prose for the
 * public glossary page ('def_long'). Sorted alphabetically. Apostrophes are
 * ignored for sort order so terms like Da'if and Du'a sort with D, Raja' with R.
 *
 * Used by:
 *   - Tooltip auto-linker in article body content (uses 'def')
 *   - page-glossary.php public page template (uses 'def_long')
 *
 * @return array<int,array{term:string,arabic:string,def:string,def_long:string}>
 */
function ce_get_glossary_terms() {
    return [
        ['term' => 'Actionalism', 'arabic' => '', 'def' => 'The principle that moral action, freely chosen, is the purpose of human existence.', 'def_long' => 'The principle that moral action, freely chosen, is the purpose of human existence. Man\'s fate is what he himself makes it — not what a saviour makes for him, not what grace bestows, not what ritual earns. Felicity (<em>falah</em>) comes from ethical effort in the real world.'],
        ['term' => 'Adha', 'arabic' => 'أذى', 'def' => 'Harm or difficulty; the Quranic term used to describe menstruation.', 'def_long' => 'Harm or difficulty. The Quranic term used in 2:222 to describe menstruation — chosen carefully to indicate physical discomfort within a context of consideration, rather than ritual impurity or moral defilement. The verse\'s instruction frames care for the woman\'s condition as the operative concern.'],
        ['term' => 'Ahad', 'arabic' => 'آحاد', 'def' => 'A solitary hadith transmitted through a single chain; carries probability, not certainty.', 'def_long' => 'A solitary hadith. A category of report transmitted through a single chain at one or more links of its transmission. Ahad reports establish probability, and the classical jurists treated them as binding for legal practice while declining to derive theological certainties from them — a methodological discipline that distinguishes Islamic scholarship from looser approaches.'],
        ['term' => 'Ahl al-Fatrah', 'arabic' => 'أهل الفترة', 'def' => 'People of the interval — those who lived without access to a prophetic message and whose judgment rests with God.', 'def_long' => 'People of the interval. Those who lived without access to a prophetic message, or whose era fell between prophets. The classical scholars held that they are accountable only for what conscience and natural reason could discern; God\'s justice does not punish ignorance of what was unreachable.'],
        ['term' => 'Akhira', 'arabic' => 'آخرة', 'def' => 'The Hereafter; the life after death.', 'def_long' => 'The Hereafter; the life after death. The Quran presents this life as preparation for the akhira, where every soul receives what it has earned — justice without approximation, mercy without limit.'],
        ['term' => 'Alaqa', 'arabic' => 'علقة', 'def' => 'A clinging or adhering form; the second stage of embryonic development described in the Quran.', 'def_long' => 'A clinging or adhering form. The second stage of embryonic development described in the Quran (23:14). Classical Arabic lexicographers documented the term\'s semantic range — clot, leech-like form, suspended thing — long before microscopy became available. The word\'s field of meaning maps onto stages of early gestation that ancient anatomy could not have observed directly.'],
        ['term' => 'Allah', 'arabic' => 'الله', 'def' => 'The proper name for God in Islam.', 'def_long' => 'The proper name for God in Islam. Where the English word \'god\' functions as a generic title, Allah names the specific One who is worshipped — the object of all devotion. The name encompasses every divine attribute: power, knowledge, mercy, justice, and the creator of everything that exists.'],
        ['term' => 'Amanah', 'arabic' => 'أمانة', 'def' => 'The trust; the moral law.', 'def_long' => 'The trust. The Quran describes God offering a trust to the heavens, the earth, and the mountains — all refused it out of fear. Man accepted it (33:72). The trust is the moral law: the obligation to freely choose good when evil is possible. It is what makes the human being unique in creation.'],
        ['term' => 'Awliyā\'', 'arabic' => 'أولياء', 'def' => 'Allies, protectors or patrons; the Quranic term for political and military allegiance.', 'def_long' => 'Allies, protectors or patrons. The plural of <em>walī</em>. In verses such as 5:51 and 60:9 the word belongs to the vocabulary of political and military alliance in time of conflict. The Quran permits kindness and justice toward those who do not fight Muslims (60:8) and permits marriage with Jewish and Christian women (5:5), so the prohibition on taking <em>awliyā\'</em> concerns allegiance against the community.'],
        ['term' => 'Ayah', 'arabic' => 'آية', 'def' => 'A sign; a verse of the Quran.', 'def_long' => 'A sign; a verse of the Quran. The Quran uses this term both for its own verses and for the signs of God in creation — the evidence pointing to the Creator. Every ayah in the universe and in revelation is an invitation to recognition.'],
        ['term' => 'Ayn', 'arabic' => 'عين', 'def' => 'The evil eye; the harmful effect of intense envy directed at a person or thing.', 'def_long' => 'The evil eye. The harmful effect of intense envy directed at a person, a possession, or a condition. Affirmed in authentic hadith and treated by the Islamic tradition as a real moral phenomenon — a feature of the moral order in which the gaze of the heart can damage what it covets. Recommended responses are prophylactic: invoking God\'s name when admiring something, and ruqya when affliction is suspected.'],
        ['term' => 'Barzakh', 'arabic' => 'برزخ', 'def' => 'The intermediate realm between death and resurrection.', 'def_long' => 'The intermediate realm between death and resurrection. A conscious state in which the soul experiences a foretaste of what awaits — comfort for those who lived well, distress for those who did not.'],
        ['term' => 'Bismillah', 'arabic' => 'بسم الله', 'def' => 'In the name of God; the phrase Muslims say before beginning any significant act.', 'def_long' => 'In the name of God. The phrase Muslims utter before beginning any significant act — eating, writing, entering a home, starting a journey. It opens 113 of the Quran\'s 114 chapters and orients the action toward God, transforming routine activity into conscious worship.'],
        ['term' => 'Da\'if', 'arabic' => 'ضعيف', 'def' => 'Weak; the grading for a hadith with a problematic chain or content.', 'def_long' => 'Weak. The grading for a hadith with a problematic chain or content — a transmitter known for poor memory, a broken link, or a textual feature that contradicts stronger evidence. Classical scholars accepted that some weak reports could be cited for moral exhortation, while excluding them from legal derivation.'],
        ['term' => 'Dawah', 'arabic' => 'دعوة', 'def' => 'Invitation; calling others to Islam through evidence.', 'def_long' => 'Invitation; calling others to Islam through evidence and reason rather than coercion. The Quran commands that dawah be conducted "with wisdom and good instruction" (16:125), respecting the autonomy of the hearer.'],
        ['term' => 'Dhikr', 'arabic' => 'ذكر', 'def' => 'Remembrance of God.', 'def_long' => 'Remembrance of God. The Quran commands it more frequently than any other practice. Includes repeated invocations, contemplative prayer, and the discipline of turning the heart\'s attention toward God until that attention becomes habitual.'],
        ['term' => 'Dhimmi', 'arabic' => 'ذمي', 'def' => 'A non-Muslim living under the protection of a Muslim state, with guaranteed life, property and worship.', 'def_long' => 'A non-Muslim living under the <em>dhimmah</em> (covenant of protection) of a Muslim state. The covenant guaranteed life, property and the free practice of religion, in return for the <em>jizyah</em> and loyalty to the state. The Prophet warned that whoever kills a person under covenant will not smell the fragrance of Paradise (Bukhari 3166).'],
        ['term' => 'Du\'a', 'arabic' => 'دعاء', 'def' => 'Personal supplication; informal prayer in any language at any time.', 'def_long' => 'Personal supplication. Informal prayer offered in any language at any time, distinct from the structured five daily prayers. The Quran describes God as near to whoever calls upon him (2:186). Du\'a expresses the relational dimension of Islamic worship — the recognition that God responds.'],
        ['term' => 'Dunya', 'arabic' => 'دنيا', 'def' => 'This world; the temporal realm.', 'def_long' => 'This world; the temporal realm as distinguished from the akhira. The dunya is not evil — it is the field of action where the trust (<em>amanah</em>) is discharged. But attachment to it at the expense of the hereafter is the root of spiritual failure.'],
        ['term' => 'Falah', 'arabic' => 'فلاح', 'def' => 'Felicity; success through ethical effort.', 'def_long' => 'Felicity; success through ethical effort. The Arabic root means "to grow vegetation from the earth" — the image of moral work producing real results in the real world. Not escape from the world, but cultivation of it.'],
        ['term' => 'Fard', 'arabic' => 'فرض', 'def' => 'An obligatory religious duty.', 'def_long' => 'An obligatory religious duty in Islamic law. Fulfilling fard obligations is the baseline of moral accountability — what is required simply by virtue of being human, not what earns extra merit.'],
        ['term' => 'Fitrah', 'arabic' => 'فطرة', 'def' => 'The innate human disposition toward recognising God.', 'def_long' => 'The innate human disposition toward recognising God. Every human being is born with it (30:30). It can be obscured by conditioning, trauma, or social pressure, but it does not disappear. The Islamic claim is that belief in God is natural; disbelief is the deviation that requires explanation.'],
        ['term' => 'Ghayb', 'arabic' => 'غيب', 'def' => 'The unseen; realities beyond empirical measurement.', 'def_long' => 'The unseen. Realities that lie beyond empirical measurement — the soul, the afterlife, angels, the Day of Judgment. Islam affirms their reality alongside the seen world (<em>shahada</em>). Belief in the unseen is not irrational; it is the recognition that reality may be larger than what instruments can detect.'],
        ['term' => 'Hadith', 'arabic' => 'حديث', 'def' => 'A report of the words, actions, or approvals of the Prophet Muhammad.', 'def_long' => 'A report of the words, actions, or approvals of the Prophet Muhammad. Hadith form the second source of Islamic guidance after the Quran, transmitted through rigorous chains of narration and evaluated by scholarly methodology.'],
        ['term' => 'Hajj', 'arabic' => 'حج', 'def' => 'The pilgrimage to Mecca.', 'def_long' => 'The pilgrimage to Mecca required once in a lifetime of every Muslim who is able. A physical journey that mirrors the spiritual journey — leaving attachments behind, standing in humility before God, and returning renewed.'],
        ['term' => 'Haram', 'arabic' => 'حرام', 'def' => 'Forbidden; prohibited by Islamic law.', 'def_long' => 'Forbidden; prohibited by Islamic law. The haram functions as a boundary protecting human flourishing — marking what destroys individuals and communities when practised. The prohibition is purposive, framed by the well-being it preserves.'],
        ['term' => 'Hasan', 'arabic' => 'حسن', 'def' => 'Good; the intermediate hadith grade, below sahih but above da\'if.', 'def_long' => 'Good; the intermediate hadith grade. A report whose chain meets most criteria for soundness while falling short of perfection in some link\'s precision. Hasan reports are accepted in legal and ethical reasoning. The grade itself signals the rigour of Islamic methodology — gradations that account for degrees of certainty.'],
        ['term' => 'Hawā', 'arabic' => 'هوى', 'def' => 'Desire; the pull of what one wishes were true.', 'def_long' => 'Desire; inclination; the pull of what one wishes were true rather than what is. The Quran warns against taking one\'s hawā as a god (45:23). Applied to intellectual life: the risk of accepting or rejecting arguments based on what one wants to be true rather than what the evidence supports.'],
        ['term' => 'Hikmah', 'arabic' => 'حكمة', 'def' => 'Wisdom; the ability to apply knowledge appropriately.', 'def_long' => 'Wisdom; the ability to apply knowledge appropriately to particular situations. The Quran distinguishes between knowing facts and possessing wisdom — the latter requires understanding purposes and contexts.'],
        ['term' => 'Ibadah', 'arabic' => 'عبادة', 'def' => 'Worship; service.', 'def_long' => 'In Islam, worship extends well beyond ritual. It encompasses every action performed for God\'s sake — work done well, kindness shown, truth spoken. Life itself becomes worship when oriented toward God.'],
        ['term' => 'Ihsan', 'arabic' => 'إحسان', 'def' => 'Excellence in worship.', 'def_long' => 'Excellence in worship; the spiritual heart of Islam. Defined by the Prophet as "worshipping God as though you see Him, and if you do not see Him, knowing that He sees you." The dimension that transforms ritual from mechanical repetition into conscious encounter with God.'],
        ['term' => 'Ijtihad', 'arabic' => 'اجتهاد', 'def' => 'Independent legal reasoning applied to new questions by a qualified scholar.', 'def_long' => 'Independent legal reasoning. The qualified scholar\'s effort to derive rulings from the Quran and Sunnah for new questions the texts do not address directly. The classical tradition has always preserved the principle of ijtihad, even in eras when its exercise narrowed; its closure would freeze Islamic law to the seventh century.'],
        ['term' => 'Ikhtilaf', 'arabic' => 'اختلاف', 'def' => 'Scholarly disagreement; the recognised Islamic tradition of jurists reaching different legal conclusions from the same sources.', 'def_long' => 'Scholarly disagreement. The recognised tradition of jurists reaching different legal conclusions from the same sources. Classical scholars treated ikhtilaf as a mercy — the diversity of valid opinions allows Islam to fit communities, climates, and circumstances the Prophet\'s generation never encountered.'],
        ['term' => 'Iman', 'arabic' => 'إيمان', 'def' => 'Faith; truth appropriated by the mind after honest evaluation.', 'def_long' => 'Faith. In the Islamic intellectual tradition, iman is a gnoseological category — a mode of knowing rather than credulity. Truth is appropriated by the mind after honest evaluation; the propositions of iman have been tested and found true.'],
        ['term' => 'Injil', 'arabic' => 'إنجيل', 'def' => 'The Gospel: the revelation God gave to Jesus, as the Quran describes it.', 'def_long' => 'The Gospel. In the Quran the Injil is a revelation God gave to Jesus, containing guidance and light and confirming the Torah (5:46). It is distinct from the four canonical Gospels of the New Testament, which are later narratives about Jesus written by others.'],
        ['term' => 'Islam', 'arabic' => 'إسلام', 'def' => 'Submission; the religion of surrendering to God\'s will.', 'def_long' => 'Submission; the religion of surrendering to God\'s will. From the same root as <em>salam</em> (peace), Islam is the state of being at peace through alignment with reality — recognising the Creator and serving Him freely.'],
        ['term' => 'Isnad', 'arabic' => 'إسناد', 'def' => 'The chain of named transmitters through whom a hadith report was passed down.', 'def_long' => 'The chain of transmitters. The named sequence of people through whom a hadith report reached its compiler. Islamic scholarship developed the science of isnad as a methodological innovation unparalleled in late antiquity, turning the question \'did he really say this?\' into a discipline with explicit criteria.'],
        ['term' => 'Jannah', 'arabic' => 'جنة', 'def' => 'Paradise; the Garden.', 'def_long' => 'The final abode of those who lived well. Its rewards include physical pleasure, yet the deeper reward is the presence of God — the satisfaction of the soul\'s longest longing for meaning and connection.'],
        ['term' => 'Jihad', 'arabic' => 'جهاد', 'def' => 'Struggle; striving.', 'def_long' => 'Struggle; striving. The greater jihad is the internal struggle against one\'s own lower inclinations; the lesser jihad is external defense of the community. Both require discipline, courage, and commitment to principle over convenience.'],
        ['term' => 'Jizyah', 'arabic' => 'جزية', 'def' => 'A tax paid by non-Muslim subjects of a Muslim state, historically in place of military service.', 'def_long' => 'A tax paid by adult non-Muslim men of means living under a Muslim state, mentioned in 9:29. Historians describe it as levied in place of military service; women, children, the elderly, the poor and clergy were generally exempt, and non-Muslims who served in the army could be exempted. In return the state guaranteed protection of life, property and worship.'],
        ['term' => 'Kafir', 'arabic' => 'كافر', 'def' => 'One who covers or denies the truth.', 'def_long' => 'One who covers or denies the truth; an unbeliever. The term describes an act of intellectual covering — not seeing what is evident — rather than a permanent identity. The door to recognition remains open as long as one lives.'],
        ['term' => 'Kashf', 'arabic' => 'كشف', 'def' => 'Spiritual unveiling; a mystical experience of direct insight.', 'def_long' => 'Spiritual unveiling. A mystical experience of direct insight, emphasised in Sufi epistemology. The Sunni mainstream treats kashf with caution: it carries no authority for legal rulings or theological doctrine, though it is acknowledged as one mode of subjective religious experience among many.'],
        ['term' => 'Khalifah', 'arabic' => 'خليفة', 'def' => 'Vicegerent; God\'s representative on earth.', 'def_long' => 'Vicegerent; God\'s representative on earth. The Quran describes God announcing to the angels: "I am placing on the earth a khalifah" (2:30). Every human being holds this status — the cosmic vocation of realising the divine moral will in freedom. It is not earned. It is appointed.'],
        ['term' => 'Khawf', 'arabic' => 'خوف', 'def' => 'Fear of God; one of three spiritual postures alongside hope and love.', 'def_long' => 'Fear of God. One of three spiritual postures alongside hope (raja\') and love (mahabbah). Khawf is moral seriousness — the awareness that actions have consequences and that the One who watches is the One who matters. The Islamic tradition treats it as the floor of religious life, paired always with hope to keep it from collapsing into despair.'],
        ['term' => 'Kufr', 'arabic' => 'كفر', 'def' => 'Disbelief; covering the truth.', 'def_long' => 'The opposite of iman. Kufr names the active rejection of what one knows or should know — distinct from honest ignorance, which the Quran treats with patience. The Quran also distinguishes those who reject after knowing from those who have not yet been reached by evidence.'],
        ['term' => 'Mahabbah', 'arabic' => 'محبة', 'def' => 'Love of God; the highest of the three spiritual postures in Islamic spirituality.', 'def_long' => 'Love of God. The highest of the three spiritual postures in Islamic spirituality. Where fear restrains and hope motivates, love transforms — making obedience natural rather than effortful. The Quran describes God as loving those who love him (5:54).'],
        ['term' => 'Maslaha', 'arabic' => 'مصلحة', 'def' => 'Public interest; a principle used in Islamic legal reasoning to address new circumstances.', 'def_long' => 'Public interest. A principle invoked in Islamic legal reasoning when no explicit text addresses a new circumstance. Maslaha allows the law to serve the purposes for which it was revealed — protection of life, intellect, lineage, property, and faith — within the discipline of textual fidelity.'],
        ['term' => 'Matn', 'arabic' => 'متن', 'def' => 'The text or content of a hadith, as distinct from its chain of transmission.', 'def_long' => 'The text of a hadith. The actual content of a report, distinct from its chain (isnad). Classical hadith critics evaluated both: an unbroken chain through reliable transmitters could still produce a rejected matn if the text contradicted the Quran, established history, or the Prophet\'s known character.'],
        ['term' => 'Muhasabah', 'arabic' => 'محاسبة', 'def' => 'Self-reckoning; spiritual discipline of examining motives.', 'def_long' => 'Self-reckoning; the spiritual discipline of examining one\'s own motives, biases, and blind spots before God. A core practice in the Islamic contemplative tradition, applied to both spiritual life and intellectual inquiry.'],
        ['term' => 'Muhaymin', 'arabic' => 'مهيمن', 'def' => 'Guardian or overseer; the Quran\'s description of its relationship to earlier scripture.', 'def_long' => 'Guardian, overseer, witness. Sūrat al-Māʾidah 5:48 describes the Quran as confirming the scripture before it and as <em>muhaymin</em> over it: the criterion that confirms what remains of earlier revelation and identifies what was altered or lost.'],
        ['term' => 'Mumin', 'arabic' => 'مؤمن', 'def' => 'A believer; one who has recognised the truth.', 'def_long' => 'A believer; one who has recognised the truth and committed to it. The mumin is distinguished not by perfection but by sincerity — the honest attempt to align life with the reality one has recognised.'],
        ['term' => 'Muslim', 'arabic' => 'مسلم', 'def' => 'One who submits to God.', 'def_long' => 'One who submits to God. The term applies to anyone who has surrendered their will to the Creator — a continuous state rather than a single event. A Muslim is always becoming, never fully arrived.'],
        ['term' => 'Mutawatir', 'arabic' => 'متواتر', 'def' => 'Mass-transmitted; a hadith or text reported through so many independent chains that fabrication is implausible.', 'def_long' => 'Mass-transmitted. A report passed down through so many independent chains, at every link, that collusion or fabrication becomes implausible. The Quran is mutawatir; a small number of hadith reach this grade. Mutawatir transmission grounds theological certainty in a way ahad reports cannot.'],
        ['term' => 'Nafs', 'arabic' => 'نفس', 'def' => 'The self; the soul; the psyche.', 'def_long' => 'The self; the soul; the psyche. The Quran describes three states of the nafs: the commanding self that urges toward evil, the blaming self that recognises wrong, and the peaceful self that has found harmony with God.'],
        ['term' => 'Niyyah', 'arabic' => 'نية', 'def' => 'Intention.', 'def_long' => 'Intention. In Islam, the moral value of an action depends on the intention behind it. Applied to intellectual inquiry: are you investigating because you want truth, or because you want permission to reach a predetermined conclusion?'],
        ['term' => 'Normativeness', 'arabic' => '', 'def' => 'The principle that God\'s existence is a moral event.', 'def_long' => 'The principle that God is not merely the first cause or a metaphysical fact — His existence is a moral event. Every attribute of God simultaneously functions as a command. To know that God is just is to know that justice is required of you. God\'s existence restructures everything.'],
        ['term' => 'Nushuz', 'arabic' => 'نشوز', 'def' => 'A serious breach of the marital bond by either spouse; the term used in 4:34 and 4:128.', 'def_long' => 'From a root meaning to rise up. A serious breach of the marital relationship, used of wives in 4:34 and of husbands in 4:128. The Quran prescribes a staged response aimed at reconciliation, followed by arbitration from both families (4:35).'],
        ['term' => 'Qiblah', 'arabic' => 'قبلة', 'def' => 'The direction of prayer; the orientation toward the Kaaba in Mecca.', 'def_long' => 'The direction of prayer. The orientation toward the Kaaba in Mecca that every Muslim assumes during salah. The qiblah unifies the daily ritual of more than a billion Muslims worldwide — a physical expression of tawhid, with all worshippers turning toward one point.'],
        ['term' => 'Quran', 'arabic' => 'القرآن', 'def' => 'The Recitation; the final revealed scripture in Islam.', 'def_long' => 'The Recitation; the final revealed scripture in Islam. The Quran claims to be the verbatim word of God revealed to Muhammad over 23 years, preserved exactly as revealed, challenging humanity to produce its like and offering itself as evidence.'],
        ['term' => 'Rahmah', 'arabic' => 'رحمة', 'def' => 'Mercy; compassion; womb-like care.', 'def_long' => 'Mercy; compassion; womb-like care. God\'s mercy encompasses all things (7:156) and precedes His wrath. The universe itself exists through mercy; punishment is only for those who actively reject it.'],
        ['term' => 'Raja\'', 'arabic' => 'رجاء', 'def' => 'Hope in God\'s mercy; one of three spiritual postures alongside fear and love.', 'def_long' => 'Hope in God\'s mercy. One of three spiritual postures alongside fear (khawf) and love (mahabbah). Raja\' keeps the believer reaching for God when sin or hardship would otherwise close the door. The Quran pairs it consistently with khawf: the two together produce balanced spiritual life.'],
        ['term' => 'Ramadan', 'arabic' => 'رمضان', 'def' => 'The ninth month of the Islamic calendar, month of fasting.', 'def_long' => 'The ninth month of the Islamic calendar, in which Muslims fast from dawn to sunset. A month of spiritual renewal, increased devotion, and community — the fast cultivates self-discipline and empathy for the less fortunate.'],
        ['term' => 'Ridwan', 'arabic' => 'رضوان', 'def' => 'God\'s pleasure; His satisfaction.', 'def_long' => 'God\'s pleasure; his satisfaction. In Islamic eschatology, paradise\'s highest reward sits above its physical comforts: <em>ridwan Allah</em> — the knowledge that the God who created you is pleased with what you became.'],
        ['term' => 'Rijal', 'arabic' => 'رجال', 'def' => 'Transmitter biographies; the scholarly literature evaluating the reliability of hadith narrators.', 'def_long' => 'Transmitter biographies. The vast scholarly literature evaluating the lives, memory, character, and reliability of hadith narrators. Some classical rijal works document tens of thousands of individuals — a documentary infrastructure for testing reports that has no parallel in any other ancient religious tradition.'],
        ['term' => 'Ruqya', 'arabic' => 'رقية', 'def' => 'Quranic recitation used for spiritual healing and protection.', 'def_long' => 'Quranic recitation for healing. The recitation of specific Quranic passages over the sick or troubled, used as protection against illness, evil eye, or spiritual harm. Where sihr seeks power outside of God, ruqya seeks God\'s power directly through his words.'],
        ['term' => 'Sahih', 'arabic' => 'صحيح', 'def' => 'Sound; the highest hadith grade, indicating a fully authenticated report.', 'def_long' => 'Sound. The highest hadith grade, indicating a fully authenticated report — unbroken chain, reliable transmitters, no hidden defects, no contradiction with stronger evidence. The two collections titled Sahih (al-Bukhari and Muslim) are treated by Sunni scholarship as the most rigorously verified hadith corpora.'],
        ['term' => 'Salah', 'arabic' => 'صلاة', 'def' => 'Prayer; the five daily ritual prayers.', 'def_long' => 'Prayer; the five daily ritual prayers that structure a Muslim\'s day. Each prayer is an appointment with God — a standing, bowing, prostrating, and sitting conversation that reconnects the soul to its Source.'],
        ['term' => 'Salam', 'arabic' => 'سلام', 'def' => 'Peace; the greeting of Muslims.', 'def_long' => 'Peace; the greeting of Muslims. Deeper than absence of conflict, salam is the peace that comes from right relationship — with God, with oneself, and with others. Paradise is <em>Dar al-Salam</em>, the Abode of Peace.'],
        ['term' => 'Sawm', 'arabic' => 'صوم', 'def' => 'Fasting.', 'def_long' => 'Fasting; abstaining from food, drink, and other physical needs from dawn to sunset. Required in Ramadan and recommended at other times, sawm cultivates self-control, empathy for the hungry, and dependence on God.'],
        ['term' => 'Shafa\'ah', 'arabic' => 'شفاعة', 'def' => 'Intercession; pleading with God on behalf of another.', 'def_long' => 'Intercession. Pleading with God on behalf of another. The Islamic tradition affirms shafa\'ah as a real possibility in the next life, granted by God\'s permission to those he chooses — the Prophet, the angels, the righteous. Permission is the operative word: shafa\'ah does not bypass God\'s authority but operates within it.'],
        ['term' => 'Shahada', 'arabic' => 'شهادة', 'def' => 'The declaration of faith.', 'def_long' => 'The declaration of faith: "There is no god but God, and Muhammad is His messenger." Also means "witnessing" — not passive repetition but active testimony to a truth one has recognised.'],
        ['term' => 'Shariah', 'arabic' => 'شريعة', 'def' => 'The Islamic law; the path to water.', 'def_long' => 'The Islamic law; the path to water. Shariah is the revealed guidance for human action — the articulation of how human beings flourish when they live according to their created nature. Every command and prohibition serves a purpose linked to that flourishing.'],
        ['term' => 'Shirk', 'arabic' => 'شرك', 'def' => 'Associating partners with God.', 'def_long' => 'Associating partners with God; the one sin the Quran declares unforgivable (4:48). It dismantles tawhid — the organising principle of all knowledge, ethics, and meaning. The category extends to elevating any authority — wealth, power, ideology, the self — to the status that belongs to God alone.'],
        ['term' => 'Sihr', 'arabic' => 'سحر', 'def' => 'Magic; sorcery; condemned in Islamic teaching.', 'def_long' => 'Magic; sorcery. Condemned in Islamic teaching as a form of shirk — the attempt to obtain power through means God has not authorised. The Quran acknowledges sihr as real and harmful while classifying it as forbidden. The category names a genuine moral risk, distinct from folkloric superstition.'],
        ['term' => 'Sunan', 'arabic' => 'سنن', 'def' => 'God\'s immutable patterns in creation.', 'def_long' => 'God\'s immutable patterns in creation. The laws of nature are sunan — constant, discoverable, and reliable because their Author does not change His way (35:43). The orderliness that makes science possible is itself a sign pointing to the One who established the patterns.'],
        ['term' => 'Sunnah', 'arabic' => 'سنة', 'def' => 'The way of the Prophet Muhammad.', 'def_long' => 'The way of the Prophet Muhammad — his teachings, actions, and approvals. The sunnah is the practical application of the Quran, showing how revelation becomes lived reality in the specific circumstances of human life.'],
        ['term' => 'Tabi\'un', 'arabic' => 'تابعون', 'def' => 'The generation after the companions who learned from them directly.', 'def_long' => 'The successors. The generation after the companions, who learned Islam directly from those who had known the Prophet. The tabi\'un form a critical link in transmission — their testimony anchors what the next generation received from people who had walked with the Prophet himself.'],
        ['term' => 'Tafakkur', 'arabic' => 'تفكر', 'def' => 'Deep reflection and contemplation; a practice commanded repeatedly in the Quran.', 'def_long' => 'Deep reflection. Contemplation of God, of creation, of one\'s own self — a practice the Quran commands repeatedly. Islamic teaching treats tafakkur as a religious obligation, fully integrated with worship rather than set apart from it. The unexamined life carries no spiritual depth in this tradition.'],
        ['term' => 'Tafsir', 'arabic' => 'تفسير', 'def' => 'Quranic exegesis; scholarly interpretation.', 'def_long' => 'Quranic exegesis; the scholarly interpretation of the Quran. Tafsir draws upon the Arabic language, the context of revelation, the sunnah, and the consensus of qualified scholars to elucidate the meanings of the sacred text.'],
        ['term' => 'Tahara', 'arabic' => 'طهارة', 'def' => 'Ritual purity; the state required for certain acts of worship such as prayer.', 'def_long' => 'Ritual purity. The state required for certain acts of worship, achieved through specific procedures of washing or, in the absence of water, symbolic substitution. Tahara concerns ritual readiness; moral cleanliness is a separate domain, addressed by repentance.'],
        ['term' => 'Tahrif', 'arabic' => 'تحريف', 'def' => 'Distortion; the Quranic charge that earlier scriptures were altered in wording or meaning.', 'def_long' => 'Distortion or alteration. The Quran says that some among earlier communities distorted words from their places and forgot a portion of what they were given (5:13). Classical scholars debated whether the alteration touched the wording, the meaning, or both.'],
        ['term' => 'Taqlid', 'arabic' => 'تقليد', 'def' => 'Blind following of authority without personal understanding or inquiry; discouraged by the Quran.', 'def_long' => 'Following authority. The Quran condemns uncritical conformity in 43:23 — \'we found our fathers on a religion\' — as a feature of unbelief. Islamic scholarship distinguishes informed deference to qualified scholars, which is permissible and often necessary, from unexamined imitation, which is rejected.'],
        ['term' => 'Taqwa', 'arabic' => 'تقوى', 'def' => 'God-consciousness; awareness of the divine presence.', 'def_long' => 'God-consciousness; awareness of the divine presence. Taqwa is the inner vigilance that keeps the khalifah aligned with his vocation. Where ordinary fear paralyses, taqwa orients — the consciousness of God becomes its own enforcement.'],
        ['term' => 'Tawbah', 'arabic' => 'توبة', 'def' => 'Repentance; return to God.', 'def_long' => 'Repentance; return to God. In Islam, always available as long as one is alive. God is described as more joyful at the repentance of His servant than a man who finds his lost camel in the desert. There is no point of no return.'],
        ['term' => 'Tawhid', 'arabic' => 'توحيد', 'def' => 'The oneness of God.', 'def_long' => 'The oneness of God. Tawhid functions as the organising principle of everything: a principle of knowledge (truth is one), ethics (the moral law flows from one source), metaphysics (creation is ordered because its Author is one), and history (humanity has one origin, one vocation, one accountability).'],
        ['term' => 'Tawrat', 'arabic' => 'توراة', 'def' => 'The Torah: the revelation God gave to Moses.', 'def_long' => 'The Torah, the revelation God gave to Moses, described in the Quran as containing guidance and light (5:44). The Quran affirms that the Torah held in Medina still contained God\'s judgement on matters brought to the Prophet (5:43).'],
        ['term' => 'Ummah', 'arabic' => 'أمة', 'def' => 'The Muslim community.', 'def_long' => 'The Muslim community; the global body of believers across all nations and ethnicities. The ummah is a spiritual fraternity — those who share the same recognition and commitment, transcending nation-state, ethnicity, and tongue.'],
        ['term' => 'Unity of Truth', 'arabic' => '', 'def' => 'The principle that if God is one, truth is one.', 'def_long' => 'The principle that if God is one, truth is one. Revelation and reason cannot ultimately contradict each other. Where they appear to, either the revelation has been misunderstood or the rational investigation is incomplete. Neither gets a blank cheque. Both must be re-examined.'],
        ['term' => 'Wadud', 'arabic' => 'الودود', 'def' => 'The Loving: one of God\'s names in the Quran.', 'def_long' => 'The Loving, the Affectionate. One of the names of God in the Quran (85:14; 11:90). The Quran speaks of a people whom God loves and who love Him (5:54), and of God loving those who do good and act justly.'],
        ['term' => 'Waswas', 'arabic' => 'وسوسة', 'def' => 'Satanic whispering; intrusive doubt.', 'def_long' => 'Satanic whispering; intrusive doubt. The Quran acknowledges it as real. The Islamic tradition distinguishes it from honest intellectual questioning — but communities have sometimes weaponised the concept to shut down all inquiry. The classical scholars did not treat doubt as unspeakable; they treated it as a station requiring careful navigation.'],
        ['term' => 'Wudu', 'arabic' => 'وضوء', 'def' => 'Ritual ablution; the washing performed before prayer.', 'def_long' => 'Ritual ablution. The washing performed before prayer — face, arms, head, feet, in a specified sequence. Wudu prepares the worshipper physically and mentally for the encounter with God; the act of cleaning marks the transition from ordinary activity to sacred attention.'],
        ['term' => 'Zakat', 'arabic' => 'زكاة', 'def' => 'Obligatory charity; wealth purification.', 'def_long' => 'Obligatory charity; wealth purification. A percentage of accumulated wealth given annually to specified categories of recipients, zakat acknowledges that property is held in trust from God and circulates resources within the community.'],
        ['term' => 'Zakat al-Fitr', 'arabic' => 'زكاة الفطر', 'def' => 'The obligatory charity given at the end of Ramadan before the Eid prayer.', 'def_long' => 'The charity of breaking the fast. The obligatory donation given at the end of Ramadan before the Eid prayer. Equal regardless of wealth — every Muslim who has the day\'s food gives the same amount on behalf of every member of their household — it ensures every believer can celebrate Eid with provision.'],
    ];
}

/**
 * Auto-link glossary terms in content
 */
function ce_auto_link_glossary_terms( $content ) {
    // Don't process if in admin or if content is empty
    if ( is_admin() || empty( $content ) ) {
        return $content;
    }

    $terms = ce_get_glossary_terms();
    $glossary_url = home_url( '/glossary/' );

    // Sort terms by length (longest first) to prevent partial replacements
    usort( $terms, function( $a, $b ) {
        return strlen( $b['term'] ) - strlen( $a['term'] );
    });

    // Create a map of terms to track what we've already linked (prevent double-linking)
    $linked_positions = [];

    // Counter shared across all terms in this content pass — gives each
    // emitted link a unique aria-describedby target id, even when the same
    // term appears multiple times.
    $describedby_counter = 0;

    foreach ( $terms as $term_data ) {
        $term = $term_data['term'];
        $arabic = $term_data['arabic'];
        $definition = esc_attr( $term_data['def'] );
        // Plain (un-escaped-as-attr) definition for the visible-but-hidden
        // span content — the span uses HTML escaping, not attribute escaping.
        $definition_text = $term_data['def'];
        $anchor = sanitize_title( $term );
        $url = $glossary_url . '#term-' . $anchor;

        // Match whole words only, case-insensitive, avoid already linked text
        $pattern = '/\b(' . preg_quote( $term, '/' ) . ')\b/i';

        $content = preg_replace_callback( $pattern, function( $matches ) use ( $url, $definition, $definition_text, $arabic, &$linked_positions, &$describedby_counter, $content, $anchor ) {
            // Check if this position is already linked
            $match_pos = strpos( $content, $matches[1] );

            // Skip if inside HTML tags or already linked
            if ( ce_is_inside_html_tag( $content, $match_pos ) || ce_is_already_linked( $content, $match_pos ) ) {
                return $matches[1];
            }

            $arabic_attr = $arabic ? ' data-arabic="' . esc_attr( $arabic ) . '"' : '';

            // Item 22 — Build a unique describedby target id and emit a
            // visually-hidden definition span the screen reader can
            // associate via aria-describedby. Sighted users get the JS
            // tooltip via data-definition (unchanged). Keyboard and
            // screen-reader users now get the same context via ARIA.
            $describedby_counter++;
            $desc_id = 'ce-gloss-' . $anchor . '-' . $describedby_counter;
            $hidden_span = '<span id="' . esc_attr( $desc_id ) . '" class="ce-glossary-sr-only">' . esc_html( $definition_text ) . '</span>';

            return '<a href="' . esc_url( $url ) . '" class="ce-glossary-term" data-definition="' . $definition . '" aria-describedby="' . esc_attr( $desc_id ) . '"' . $arabic_attr . '>' . $matches[1] . '</a>' . $hidden_span;
        }, $content, 3 ); // Limit to 3 replacements per term per article
    }

    return $content;
}

/**
 * Check if position is inside an HTML tag
 */
function ce_is_inside_html_tag( $content, $position ) {
    $before = substr( $content, 0, $position );
    $lt_count = substr_count( $before, '<' );
    $gt_count = substr_count( $before, '>' );
    return $lt_count > $gt_count;
}

/**
 * Check if position is already inside a link
 */
function ce_is_already_linked( $content, $position ) {
    $before = substr( $content, 0, $position );
    $after = substr( $content, $position );

    // Check for opening <a before and closing </a> after
    $last_open_a = strrpos( $before, '<a ' );
    $last_close_a = strrpos( $before, '</a>' );

    if ( $last_open_a !== false && ( $last_close_a === false || $last_open_a > $last_close_a ) ) {
        return true;
    }

    return false;
}

/**
 * Enqueue glossary tooltip assets
 */
function ce_glossary_enqueue_assets() {
    // Only on single articles and posts
    if ( ! is_singular( ['ce_article', 'post'] ) ) {
        return;
    }

    wp_enqueue_style(
        'ce-glossary-tooltip',
        get_stylesheet_directory_uri() . '/assets/css/glossary-tooltip.css',
        [],
        wp_get_theme()->get('Version')
    );

    wp_enqueue_script(
        'ce-glossary-tooltip',
        get_stylesheet_directory_uri() . '/assets/js/glossary-tooltip.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'ce_glossary_enqueue_assets' );

/**
 * Apply auto-linking to article content
 */
function ce_glossary_filter_content( $content ) {
    if ( is_singular( ['ce_article', 'post'] ) ) {
        $content = ce_auto_link_glossary_terms( $content );
    }
    return $content;
}
// Content filter removed — handled by unified engine in ce-crosslinks.php
// add_filter( 'the_content', 'ce_glossary_filter_content', 20 );

/**
 * Shortcode to display a glossary term with tooltip
 * Usage: [glossary term="Tawhid"]
 */
function ce_glossary_term_shortcode( $atts ) {
    $atts = shortcode_atts( [
        'term' => '',
    ], $atts );

    if ( empty( $atts['term'] ) ) {
        return '';
    }

    $terms = ce_get_glossary_terms();
    $found = null;

    foreach ( $terms as $t ) {
        if ( strcasecmp( $t['term'], $atts['term'] ) === 0 ) {
            $found = $t;
            break;
        }
    }

    if ( ! $found ) {
        return esc_html( $atts['term'] );
    }

    $anchor = sanitize_title( $found['term'] );
    $url = home_url( '/glossary/#term-' . $anchor );
    $arabic_attr = $found['arabic'] ? ' data-arabic="' . esc_attr( $found['arabic'] ) . '"' : '';

    // Item 22 — accessibility describedby. Each shortcode invocation gets
    // its own unique id via wp_unique_id() so multiple uses of the same
    // term on a page don't collide.
    static $shortcode_counter = 0;
    $shortcode_counter++;
    $desc_id = 'ce-gloss-sc-' . $anchor . '-' . $shortcode_counter;
    $hidden_span = '<span id="' . esc_attr( $desc_id ) . '" class="ce-glossary-sr-only">' . esc_html( $found['def'] ) . '</span>';

    return '<a href="' . esc_url( $url ) . '" class="ce-glossary-term" data-definition="' . esc_attr( $found['def'] ) . '" aria-describedby="' . esc_attr( $desc_id ) . '"' . $arabic_attr . '>' . esc_html( $found['term'] ) . '</a>' . $hidden_span;
}
add_shortcode( 'glossary', 'ce_glossary_term_shortcode' );
