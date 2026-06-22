// Load current config on page load
document.addEventListener('DOMContentLoaded', async () => {
    await loadConfig();
    
    document.getElementById('configForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        await saveConfig();
    });
});

async function loadConfig() {
    try {
        const response = await fetch('/api/config');
        const config = await response.json();
        
        document.getElementById('pexels_api_key').value = config.pexels_api_key || '';
        document.getElementById('pixabay_api_key').value = config.pixabay_api_key || '';
    } catch (error) {
        console.error('Failed to load config:', error);
        showMessage('Failed to load configuration', 'error');
    }
}

async function saveConfig() {
    const pexelsKey = document.getElementById('pexels_api_key').value.trim();
    const pixabayKey = document.getElementById('pixabay_api_key').value.trim();
    
    if (!pexelsKey || !pixabayKey) {
        showMessage('Please fill in both API keys', 'error');
        return;
    }
    
    try {
        const response = await fetch('/api/config', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                pexels_api_key: pexelsKey,
                pixabay_api_key: pixabayKey
            })
        });
        
        if (response.ok) {
            showMessage('✅ Configuration saved successfully!', 'success');
        } else {
            showMessage('❌ Failed to save configuration', 'error');
        }
    } catch (error) {
        console.error('Failed to save config:', error);
        showMessage('❌ Failed to save configuration', 'error');
    }
}

function showMessage(message, type) {
    const container = document.getElementById('messageContainer');
    container.classList.remove('hidden');
    container.textContent = message;
    container.className = `mb-6 p-4 rounded-xl border ${
        type === 'success' 
            ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-400' 
            : 'border-red-500/30 bg-red-500/10 text-red-400'
    }`;
}
