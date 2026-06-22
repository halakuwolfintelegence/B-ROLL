// API Configuration - Load from Cloudflare environment
let API_CONFIG = {
    PEXELS_API_KEY: '',
    PIXABAY_API_KEY: ''
};

// Load config from server
async function loadConfig() {
    try {
        const response = await fetch('/api/config');
        const config = await response.json();
        API_CONFIG = config;
    } catch (error) {
        console.error('Failed to load config:', error);
        // Fallback to default keys
        API_CONFIG = {
            PEXELS_API_KEY: 'hPfLL2XaPl3rVFEHXNaQbZstXrX1vZMSxmuvN9tqrAwbpXSZhdVL3Blm',
            PIXABAY_API_KEY: '56395196-037a4e0daa26799bb7627b4f3'
        };
    }
}

// Premium Pure Video Fallback Pool
const premiumVideoPool = [
    { tags: ['ai', 'tech', 'future', 'data', 'work'], url: 'https://assets.mixkit.co/videos/preview/mixkit-man-holding-a-smartphone-with-a-blue-screen-40176-large.mp4', thumb: 'https://images.pexels.com/photos/6153354/pexels-photo-6153354.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Artgrid Stream Node' },
    { tags: ['cyberpunk', 'city', 'night', 'rain', 'neon'], url: 'https://assets.mixkit.co/videos/preview/mixkit-time-lapse-of-a-city-at-night-4158-large.mp4', thumb: 'https://images.pexels.com/photos/1612513/pexels-photo-1612513.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Coverr Premium Video' },
    { tags: ['nature', 'forest', 'calm', 'trees', 'morning'], url: 'https://assets.mixkit.co/videos/preview/mixkit-aerial-view-of-a-dense-forest-2280-large.mp4', thumb: 'https://images.pexels.com/photos/3225517/pexels-photo-3225517.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Mixkit Video Node' },
    { tags: ['code', 'work', 'office', 'software', 'ai'], url: 'https://assets.mixkit.co/videos/preview/mixkit-coding-on-a-computer-screen-with-a-neon-light-42217-large.mp4', thumb: 'https://images.pexels.com/photos/546814/pexels-photo-546814.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Coverr Core Video' },
    { tags: ['business', 'team', 'meeting', 'work'], url: 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-man-typing-on-a-laptop-4173-large.mp4', thumb: 'https://images.pexels.com/photos/3183150/pexels-photo-3183150.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Storyblocks Video Node' },
    { tags: ['ocean', 'water', 'beach', 'waves', 'nature'], url: 'https://assets.mixkit.co/videos/preview/mixkit-waves-coming-to-the-beach-5016-large.mp4', thumb: 'https://images.pexels.com/photos/1001682/pexels-photo-1001682.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Envato Elements Video' },
    { tags: ['space', 'stars', 'galaxy', 'universe', 'future'], url: 'https://assets.mixkit.co/videos/preview/mixkit-stars-in-space-1610-large.mp4', thumb: 'https://images.pexels.com/photos/116975/pexels-photo-116975.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Videezy Pro Video' },
    { tags: ['cyberpunk', 'neon', 'city', 'night'], url: 'https://assets.mixkit.co/videos/preview/mixkit-neon-light-from-a-building-signage-at-night-42220-large.mp4', thumb: 'https://images.pexels.com/photos/2387873/pexels-photo-2387873.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Cyber Matrix Node' },
    { tags: ['nature', 'calm', 'morning', 'water'], url: 'https://assets.mixkit.co/videos/preview/mixkit-sunlight-filtering-through-trees-near-a-river-43034-large.mp4', thumb: 'https://images.pexels.com/photos/1424971/pexels-photo-1424971.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'Vidsplay HD Node' },
    { tags: ['ai', 'tech', 'code', 'data'], url: 'https://assets.mixkit.co/videos/preview/mixkit-abstract-laser-lights-background-41855-large.mp4', thumb: 'https://images.pexels.com/photos/2582937/pexels-photo-2582937.jpeg?auto=compress&cs=tinysrgb&w=400', engine: 'MotionArray Core' }
];

// Initialize on page load
document.addEventListener('DOMContentLoaded', async () => {
    await loadConfig();
    
    document.getElementById('processBtn').addEventListener('click', processScript);
});

async function processScript() {
    const scriptText = document.getElementById('scriptInput').value.trim();
    if (!scriptText) { 
        alert('Please enter your script parameters into the workspace.'); 
        return; 
    }

    const container = document.getElementById('resultsContainer');
    const loader = document.getElementById('loader');
    const loaderText = document.getElementById('loaderText');
    const status = document.getElementById('statusBadge');
    
    container.innerHTML = '';
    loader.classList.remove('hidden');
    status.classList.remove('hidden');
    status.textContent = 'Mapping Scenes...';

    // Split on newlines OR sentence endings
    const lines = scriptText
        .split(/\n+|(?<=[.!?])\s+(?=[A-Z])/)
        .map(l => l.trim())
        .filter(l => l.length > 5);

    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        loaderText.textContent = `AI Processing Sequence #${i+1}...`;
        
        const keywords = extractKeywords(line);
        const displayKeywords = keywords.length > 0 ? keywords : ['abstract', 'motion'];

        const sceneElement = document.createElement('div');
        sceneElement.className = "bg-slate-900/40 p-5 rounded-2xl border border-slate-800/50 space-y-4 shadow-sm animate-fade-in";
        sceneElement.innerHTML = `
            <div class="flex flex-wrap justify-between items-start gap-3 border-b border-slate-800/60 pb-3">
                <p class="text-xs sm:text-sm font-medium text-slate-300 max-w-xl leading-relaxed">
                    <span class="text-cyan-400 font-bold font-mono mr-1">Scene #${i+1}</span> "${line}"
                </p>
                <div class="flex gap-1 text-[10px] bg-slate-950 px-2.5 py-0.5 rounded-md text-cyan-400 font-mono tracking-wider border border-cyan-900/30 uppercase font-bold">
                    Tags: ${displayKeywords.join(', ')}
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 video-grid-target">
                <div class="col-span-full text-center py-4 text-xs text-slate-600 animate-pulse font-light tracking-wide">Querying remote stock assets...</div>
            </div>
        `;
        container.appendChild(sceneElement);

        await dispatchMediaRequests(displayKeywords, sceneElement.querySelector('.video-grid-target'));
        
        // Rate Limiting - 1.2 second delay between requests
        if (i < lines.length - 1) {
            await new Promise(resolve => setTimeout(resolve, 1200));
        }
    }

    loader.classList.add('hidden');
    status.textContent = `Completed (${lines.length} Sequences Mixed)`;
}

function extractKeywords(text) {
    const stopWords = new Set(['i','me','my','the','and','but','if','or','because','as','until','while','of','at','by','for','with','about','in','on','at','under','underneath','this','that','these','those','then']);
    return text.toLowerCase().replace(/[^a-zA-Z\s]/g, '').split(/\s+/).filter(word => word.length > 2 && !stopWords.has(word)).slice(0, 3);
}

async function dispatchMediaRequests(keywords, gridTarget) {
    const query = encodeURIComponent(keywords.join(' '));
    let collectedPool = [];
    let seenUrls = new Set();

    // 1. Fetch Pexels Network Node
    if (API_CONFIG.PEXELS_API_KEY) {
        try {
            const res = await fetch(`https://api.pexels.com/videos/search?query=${query}&per_page=8&orientation=landscape`, { 
                headers: { Authorization: API_CONFIG.PEXELS_API_KEY }
            });
            if (res.ok) {
                const data = await res.json();
                if (data.videos) {
                    data.videos.forEach(v => {
                        const file = v.video_files.find(f => f.quality === 'hd' || f.width >= 1280) || v.video_files[0];
                        if (file && !seenUrls.has(file.link)) {
                            seenUrls.add(file.link);
                            collectedPool.push({ 
                                source: 'Pexels Video Node', 
                                pageUrl: v.url, 
                                videoUrl: file.link, 
                                previewImg: v.image 
                            });
                        }
                    });
                }
            }
        } catch (e) { console.error("Pexels lookup fault", e); }
    }

    // 2. Fetch Pixabay Stream Node
    if (API_CONFIG.PIXABAY_API_KEY && collectedPool.length < 6) {
        try {
            const res = await fetch(`https://pixabay.com/api/videos/?key=${API_CONFIG.PIXABAY_API_KEY}&q=${query}&per_page=8&orientation=landscape`);
            if (res.ok) {
                const data = await res.json();
                if (data.hits) {
                    data.hits.forEach(v => {
                        const file = v.videos.medium || v.videos.small;
                        if (file && !seenUrls.has(file.url)) {
                            seenUrls.add(file.url);
                            collectedPool.push({ 
                                source: 'Pixabay Video Node', 
                                pageUrl: v.pageURL, 
                                videoUrl: file.url, 
                                previewImg: `https://i.vimeocdn.com/video/${v.picture_id}_640x360.jpg` 
                            });
                        }
                    });
                }
            }
        } catch (e) { console.error("Pixabay lookup fault", e); }
    }

    // 3. Fallback Mix - Ensures exactly 6 unique videos
    if (collectedPool.length < 6) {
        let matchedFallbacks = premiumVideoPool.filter(item => 
            item.tags.some(tag => keywords.includes(tag))
        );
        let allFallbacks = [...premiumVideoPool].sort(() => 0.5 - Math.random());
        let combinedFallbacks = [...matchedFallbacks, ...allFallbacks];

        for (let item of combinedFallbacks) {
            if (collectedPool.length >= 6) break;
            if (!seenUrls.has(item.url)) {
                seenUrls.add(item.url);
                collectedPool.push({ 
                    source: item.engine, 
                    pageUrl: item.url, 
                    videoUrl: item.url, 
                    previewImg: item.thumb 
                });
            }
        }
    }

    // Enforce exactly 6 unique items
    collectedPool = collectedPool.slice(0, 6);

    gridTarget.innerHTML = '';
    collectedPool.forEach((vid) => {
        const card = document.createElement('div');
        card.className = "relative group aspect-video bg-slate-950 rounded-xl overflow-hidden border border-slate-800/40 video-card shadow-lg transition-all duration-300 hover:border-cyan-500/30 hover:shadow-[0_4px_20px_rgba(6,182,212,0.1)]";
        card.innerHTML = `
            <img src="${vid.previewImg}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" onerror="this.src='https://images.pexels.com/photos/3165335/pexels-photo-3165335.jpeg?auto=compress&cs=tinysrgb&w=300'">
            <div class="absolute inset-0 bg-slate-950/90 opacity-0 video-overlay transition-opacity duration-200 flex flex-col justify-between p-3.5">
                <span class="self-start text-[9px] uppercase font-bold tracking-widest px-2 py-0.5 rounded bg-slate-900 border border-slate-800/80 text-cyan-400">
                    ${vid.source}
                </span>
                <div class="space-y-2">
                    <a href="${vid.videoUrl}" target="_blank" download class="w-full text-center bg-gradient-to-r from-cyan-400 to-indigo-500 hover:opacity-95 text-slate-950 text-xs font-black py-2 px-3 rounded-lg transition-all flex items-center justify-center gap-1.5 tracking-wider uppercase cursor-pointer shadow-md">
                        📥 Download Video
                    </a>
                    <a href="${vid.videoUrl}" target="_blank" class="block text-center bg-slate-900/80 hover:bg-slate-800 text-slate-400 text-[10px] py-1.5 px-2 rounded-lg border border-slate-800/60 transition-colors tracking-wide">
                        Preview Video
                    </a>
                </div>
            </div>
        `;
        gridTarget.appendChild(card);
    });
}
