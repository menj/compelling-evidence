<?php
/**
 * Template Name: Glossary
 * Description: Alphabetical glossary of Islamic terms used throughout the site
 */
get_header();

$terms = [
    ['term' => 'Actionalism', 'arabic' => '', 'def' => 'The principle that moral action, freely chosen, is the purpose of human existence. Man\'s fate is what he himself makes it — not what a saviour makes for him, not what grace bestows, not what ritual earns. Felicity (<em>falah</em>) comes from ethical effort in the real world.'],
    ['term' => 'Akhira', 'arabic' => 'آخرة', 'def' => 'The Hereafter; the life after death. The Quran presents this life as preparation for the akhira, where every soul receives what it has earned — justice without approximation, mercy without limit.'],
    ['term' => 'Allah', 'arabic' => 'الله', 'def' => 'The proper name for God in Islam — not a generic title but a proper noun signifying the One who is worshipped, the object of all devotion. The name encompasses all divine attributes: power, knowledge, mercy, justice, and the creator of everything that exists.'],
    ['term' => 'Amanah', 'arabic' => 'أمانة', 'def' => 'The trust. The Quran describes God offering a trust to the heavens, the earth, and the mountains — all refused it out of fear. Man accepted it (33:72). The trust is the moral law: the obligation to freely choose good when evil is possible. It is what makes the human being unique in creation.'],
    ['term' => 'Ayah', 'arabic' => 'آية', 'def' => 'A sign; a verse of the Quran. The Quran uses this term both for its own verses and for the signs of God in creation — the evidence pointing to the Creator. Every ayah in the universe and in revelation is an invitation to recognition.'],
    ['term' => 'Barzakh', 'arabic' => 'برزخ', 'def' => 'The intermediate realm between death and resurrection. A conscious state in which the soul experiences a foretaste of what awaits — comfort for those who lived well, distress for those who did not.'],
    ['term' => 'Dawah', 'arabic' => 'دعوة', 'def' => 'Invitation; calling others to Islam through evidence and reason rather than coercion. The Quran commands that dawah be conducted "with wisdom and good instruction" (16:125), respecting the autonomy of the hearer.'],
    ['term' => 'Dhikr', 'arabic' => 'ذكر', 'def' => 'Remembrance of God. The Quran commands it more frequently than any other practice. Includes repeated invocations, contemplative prayer, and the discipline of turning the heart\'s attention toward God until that attention becomes habitual.'],
    ['term' => 'Dunya', 'arabic' => 'دنيا', 'def' => 'This world; the temporal realm as distinguished from the akhira. The dunya is not evil — it is the field of action where the trust (<em>amanah</em>) is discharged. But attachment to it at the expense of the hereafter is the root of spiritual failure.'],
    ['term' => 'Falah', 'arabic' => 'فلاح', 'def' => 'Felicity; success through ethical effort. The Arabic root means "to grow vegetation from the earth" — the image of moral work producing real results in the real world. Not escape from the world, but cultivation of it.'],
    ['term' => 'Fard', 'arabic' => 'فرض', 'def' => 'An obligatory religious duty in Islamic law. Fulfilling fard obligations is the baseline of moral accountability — what is required simply by virtue of being human, not what earns extra merit.'],
    ['term' => 'Fitrah', 'arabic' => 'فطرة', 'def' => 'The innate human disposition toward recognising God. Every human being is born with it (30:30). It can be obscured by conditioning, trauma, or social pressure, but it does not disappear. The Islamic claim is that belief in God is natural; disbelief is the deviation that requires explanation.'],
    ['term' => 'Ghayb', 'arabic' => 'غيب', 'def' => 'The unseen. Realities that lie beyond empirical measurement — the soul, the afterlife, angels, the Day of Judgment. Islam affirms their reality alongside the seen world (<em>shahada</em>). Belief in the unseen is not irrational; it is the recognition that reality may be larger than what instruments can detect.'],
    ['term' => 'Hadith', 'arabic' => 'حديث', 'def' => 'A report of the words, actions, or approvals of the Prophet Muhammad. Hadith form the second source of Islamic guidance after the Quran, transmitted through rigorous chains of narration and evaluated by scholarly methodology.'],
    ['term' => 'Hajj', 'arabic' => 'حج', 'def' => 'The pilgrimage to Mecca required once in a lifetime of every Muslim who is able. A physical journey that mirrors the spiritual journey — leaving attachments behind, standing in humility before God, and returning renewed.'],
    ['term' => 'Haram', 'arabic' => 'حرام', 'def' => 'Forbidden; prohibited by Islamic law. The haram is not arbitrary restriction but the boundary protecting human flourishing — what destroys individuals and communities when practised.'],
    ['term' => 'Hawā', 'arabic' => 'هوى', 'def' => 'Desire; inclination; the pull of what one wishes were true rather than what is. The Quran warns against taking one\'s hawā as a god (45:23). Applied to intellectual life: the risk of accepting or rejecting arguments based on what one wants to be true rather than what the evidence supports.'],
    ['term' => 'Hikmah', 'arabic' => 'حكمة', 'def' => 'Wisdom; the ability to apply knowledge appropriately to particular situations. The Quran distinguishes between knowing facts and possessing wisdom — the latter requires understanding purposes and contexts.'],
    ['term' => 'Ibadah', 'arabic' => 'عبادة', 'def' => 'Worship; service. In Islam, worship is not limited to ritual but encompasses every action performed for God\'s sake — work done well, kindness shown, truth spoken. Life itself becomes worship when oriented toward God.'],
    ['term' => 'Ihsan', 'arabic' => 'إحسان', 'def' => 'Excellence in worship; the spiritual heart of Islam. Defined by the Prophet as "worshipping God as though you see Him, and if you do not see Him, knowing that He sees you." The dimension that transforms ritual from mechanical repetition into conscious encounter with God.'],
    ['term' => 'Iman', 'arabic' => 'إيمان', 'def' => 'Not "blind faith" but a mode of knowing — truth appropriated by the mind after honest evaluation. In the Islamic intellectual tradition, iman is a gnoseological category: it has to do with knowledge, not credulity. The propositions of iman have been tested and found true.'],
    ['term' => 'Islam', 'arabic' => 'إسلام', 'def' => 'Submission; the religion of surrendering to God\'s will. From the same root as <em>salam</em> (peace), Islam is the state of being at peace through alignment with reality — recognising the Creator and serving Him freely.'],
    ['term' => 'Jannah', 'arabic' => 'جنة', 'def' => 'Paradise; the Garden. The final abode of those who lived well — not merely physical pleasure but the presence of God, the satisfaction of the soul\'s deepest longing for meaning and connection.'],
    ['term' => 'Jihad', 'arabic' => 'جهاد', 'def' => 'Struggle; striving. The greater jihad is the internal struggle against one\'s own lower inclinations; the lesser jihad is external defense of the community. Both require discipline, courage, and commitment to principle over convenience.'],
    ['term' => 'Kafir', 'arabic' => 'كافر', 'def' => 'One who covers or denies the truth; an unbeliever. The term describes an act of intellectual covering — not seeing what is evident — rather than a permanent identity. The door to recognition remains open as long as one lives.'],
    ['term' => 'Khalifah', 'arabic' => 'خليفة', 'def' => 'Vicegerent; God\'s representative on earth. The Quran describes God announcing to the angels: "I am placing on the earth a khalifah" (2:30). Every human being holds this status — the cosmic vocation of realising the divine moral will in freedom. It is not earned. It is appointed.'],
    ['term' => 'Kufr', 'arabic' => 'كفر', 'def' => 'Disbelief; covering the truth. The opposite of iman — not mere ignorance but the active rejection of what one knows or should know. The Quran distinguishes between those who reject after knowing and those who have not yet been reached by evidence.'],
    ['term' => 'Mumin', 'arabic' => 'مؤمن', 'def' => 'A believer; one who has recognised the truth and committed to it. The mumin is distinguished not by perfection but by sincerity — the honest attempt to align life with the reality one has recognised.'],
    ['term' => 'Muhasabah', 'arabic' => 'محاسبة', 'def' => 'Self-reckoning; the spiritual discipline of examining one\'s own motives, biases, and blind spots before God. A core practice in the Islamic contemplative tradition, applied to both spiritual life and intellectual inquiry.'],
    ['term' => 'Muslim', 'arabic' => 'مسلم', 'def' => 'One who submits to God. The term applies to anyone who has surrendered their will to the Creator — a continuous state rather than a single event. A Muslim is always becoming, never fully arrived.'],
    ['term' => 'Nafs', 'arabic' => 'نفس', 'def' => 'The self; the soul; the psyche. The Quran describes three states of the nafs: the commanding self that urges toward evil, the blaming self that recognises wrong, and the peaceful self that has found harmony with God.'],
    ['term' => 'Niyyah', 'arabic' => 'نية', 'def' => 'Intention. In Islam, the moral value of an action depends on the intention behind it. Applied to intellectual inquiry: are you investigating because you want truth, or because you want permission to reach a predetermined conclusion?'],
    ['term' => 'Normativeness', 'arabic' => '', 'def' => 'The principle that God is not merely the first cause or a metaphysical fact — His existence is a moral event. Every attribute of God simultaneously functions as a command. To know that God is just is to know that justice is required of you. God\'s existence restructures everything.'],
    ['term' => 'Quran', 'arabic' => 'القرآن', 'def' => 'The Recitation; the final revealed scripture in Islam. The Quran claims to be the verbatim word of God revealed to Muhammad over 23 years, preserved exactly as revealed, challenging humanity to produce its like and offering itself as evidence.'],
    ['term' => 'Rahmah', 'arabic' => 'رحمة', 'def' => 'Mercy; compassion; womb-like care. God\'s mercy encompasses all things (7:156) and precedes His wrath. The universe itself exists through mercy; punishment is only for those who actively reject it.'],
    ['term' => 'Ramadan', 'arabic' => 'رمضان', 'def' => 'The ninth month of the Islamic calendar, in which Muslims fast from dawn to sunset. A month of spiritual renewal, increased devotion, and community — the fast cultivates self-discipline and empathy for the less fortunate.'],
    ['term' => 'Ridwan', 'arabic' => 'رضوان', 'def' => 'God\'s pleasure; His satisfaction. In Islamic eschatology, the highest reward of paradise is not its physical comforts but <em>ridwan Allah</em> — the knowledge that the God who created you is pleased with what you became.'],
    ['term' => 'Salah', 'arabic' => 'صلاة', 'def' => 'Prayer; the five daily ritual prayers that structure a Muslim\'s day. Each prayer is an appointment with God — a standing, bowing, prostrating, and sitting conversation that reconnects the soul to its Source.'],
    ['term' => 'Salam', 'arabic' => 'سلام', 'def' => 'Peace; the greeting of Muslims. Deeper than absence of conflict, salam is the peace that comes from right relationship — with God, with oneself, and with others. Paradise is <em>Dar al-Salam</em>, the Abode of Peace.'],
    ['term' => 'Sawm', 'arabic' => 'صوم', 'def' => 'Fasting; abstaining from food, drink, and other physical needs from dawn to sunset. Required in Ramadan and recommended at other times, sawm cultivates self-control, empathy for the hungry, and dependence on God.'],
    ['term' => 'Shahada', 'arabic' => 'شهادة', 'def' => 'The declaration of faith: "There is no god but God, and Muhammad is His messenger." Also means "witnessing" — not passive repetition but active testimony to a truth one has recognised.'],
    ['term' => 'Shariah', 'arabic' => 'شريعة', 'def' => 'The Islamic law; the path to water. Shariah is the revealed guidance for human action — not arbitrary rules but the articulation of how human beings flourish when they live according to their created nature.'],
    ['term' => 'Shirk', 'arabic' => 'شرك', 'def' => 'Associating partners with God; the one sin the Quran declares unforgivable (4:48). It dismantles tawhid — the organising principle of all knowledge, ethics, and meaning. Includes not only worshipping other deities but elevating any authority to the status that belongs to God alone.'],
    ['term' => 'Sunnah', 'arabic' => 'سنة', 'def' => 'The way of the Prophet Muhammad — his teachings, actions, and approvals. The sunnah is the practical application of the Quran, showing how revelation becomes lived reality in the specific circumstances of human life.'],
    ['term' => 'Sunan', 'arabic' => 'سنن', 'def' => 'God\'s immutable patterns in creation. The laws of nature are sunan — constant, discoverable, and reliable because their Author does not change His way (35:43). The orderliness that makes science possible is itself a sign pointing to the One who established the patterns.'],
    ['term' => 'Tafsir', 'arabic' => 'تفسير', 'def' => 'Quranic exegesis; the scholarly interpretation of the Quran. Tafsir draws upon the Arabic language, the context of revelation, the sunnah, and the consensus of qualified scholars to elucidate the meanings of the sacred text.'],
    ['term' => 'Taqwa', 'arabic' => 'تقوى', 'def' => 'God-consciousness; awareness of the divine presence. Not fear in the ordinary sense but the inner vigilance that keeps the khalifah aligned with his vocation. The person with taqwa does not need external enforcement to behave justly — the consciousness of God is its own enforcement.'],
    ['term' => 'Tawhid', 'arabic' => 'توحيد', 'def' => 'The oneness of God — not merely a theological doctrine but the organising principle of everything. Simultaneously a principle of knowledge (truth is one), ethics (the moral law flows from one source), metaphysics (creation is ordered because its Author is one), and history (humanity has one origin, one vocation, one accountability).'],
    ['term' => 'Tawbah', 'arabic' => 'توبة', 'def' => 'Repentance; return to God. In Islam, always available as long as one is alive. God is described as more joyful at the repentance of His servant than a man who finds his lost camel in the desert. There is no point of no return.'],
    ['term' => 'Ummah', 'arabic' => 'أمة', 'def' => 'The Muslim community; the global body of believers across all nations and ethnicities. The ummah is not a nation-state but a spiritual fraternity — those who share the same recognition and commitment, transcending all other affiliations.'],
    ['term' => 'Unity of Truth', 'arabic' => '', 'def' => 'The principle that if God is one, truth is one. Revelation and reason cannot ultimately contradict each other. Where they appear to, either the revelation has been misunderstood or the rational investigation is incomplete. Neither gets a blank cheque. Both must be re-examined.'],
    ['term' => 'Waswas', 'arabic' => 'وسوسة', 'def' => 'Satanic whispering; intrusive doubt. The Quran acknowledges it as real. The Islamic tradition distinguishes it from honest intellectual questioning — but communities have sometimes weaponised the concept to shut down all inquiry. The classical scholars did not treat doubt as unspeakable; they treated it as a station requiring careful navigation.'],
    ['term' => 'Zakat', 'arabic' => 'زكاة', 'def' => 'Obligatory charity; wealth purification. A percentage of accumulated wealth given annually to specified categories of recipients, zakat acknowledges that property is held in trust from God and circulates resources within the community.'],
];

// Group by first letter
$grouped = [];
foreach ( $terms as $t ) {
    $letter = strtoupper( mb_substr( $t['term'], 0, 1 ) );
    $grouped[ $letter ][] = $t;
}
ksort( $grouped );
?>

<main class="ce-page ce-glossary" id="main">

  <header class="page-hero glossary-hero">
    <div class="page-hero-inner">
      <p class="section-label">Reference</p>
      <h1>Glossary of Terms</h1>
      <p class="hero-sub">Key concepts from the Islamic intellectual tradition used throughout this site — defined clearly and without jargon.</p>
    </div>
  </header>

  <div class="glossary-body">

    <nav class="glossary-alphabet" aria-label="Jump to letter">
      <?php foreach ( array_keys( $grouped ) as $letter ) : ?>
        <a href="#letter-<?php echo esc_attr( $letter ); ?>"><?php echo esc_html( $letter ); ?></a>
      <?php endforeach; ?>
    </nav>

    <?php foreach ( $grouped as $letter => $letter_terms ) : ?>
      <section class="glossary-group" id="letter-<?php echo esc_attr( $letter ); ?>">
        <h2 class="glossary-letter"><?php echo esc_html( $letter ); ?></h2>
        <?php foreach ( $letter_terms as $t ) : ?>
          <div class="glossary-entry" id="term-<?php echo esc_attr( sanitize_title( $t['term'] ) ); ?>">
            <dt class="glossary-term">
              <?php echo esc_html( $t['term'] ); ?>
              <?php if ( $t['arabic'] ) : ?>
                <span class="glossary-arabic" lang="ar" dir="rtl"><?php echo esc_html( $t['arabic'] ); ?></span>
              <?php endif; ?>
            </dt>
            <dd class="glossary-def"><?php echo wp_kses_post( $t['def'] ); ?></dd>
          </div>
        <?php endforeach; ?>
      </section>
    <?php endforeach; ?>

  </div>

<?php
// DefinedTermSet schema markup
$term_schema = [];
foreach ( $terms as $t ) {
    $term_schema[] = [
        '@type'       => 'DefinedTerm',
        'name'        => $t['term'],
        'termCode'    => $t['arabic'] ?: null,
        'description' => wp_strip_all_tags( $t['def'] ),
    ];
}
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "DefinedTermSet",
  "name": "Compelling Evidence Glossary of Islamic Terms",
  "description": "Key concepts from the Islamic intellectual tradition used throughout this site — defined clearly and without jargon.",
  "inLanguage": "en",
  "publisher": {
    "@type": "Organization",
    "name": "Compelling Evidence",
    "url": "https://compelling-evidence.com"
  },
  "hasDefinedTerm": <?php echo wp_json_encode( $term_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>
}
</script>

<!-- Speakable schema for glossary terms -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "speakable": {
    "@type": "SpeakableSpecification",
    "cssSelector": ["h1", ".glossary-entry"]
  }
}
</script>

</main>

<?php get_footer(); ?>
