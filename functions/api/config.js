/**
 * API endpoint for managing configuration
 * Works with Cloudflare Pages Functions
 */

export async function onRequest(context) {
    const { request, env } = context;
    
    // Handle CORS
    const headers = {
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Methods': 'GET, POST, OPTIONS',
        'Access-Control-Allow-Headers': 'Content-Type'
    };
    
    // Handle OPTIONS request (CORS preflight)
    if (request.method === 'OPTIONS') {
        return new Response(null, { headers, status: 204 });
    }
    
    // Handle GET request
    if (request.method === 'GET') {
        try {
            // Get config from KV storage
            let config = await env.KV_NAMESPACE.get('config', 'json');
            
            if (!config) {
                // Default config from environment variables
                config = {
                    pexels_api_key: env.PEXELS_API_KEY || '',
                    pixabay_api_key: env.PIXABAY_API_KEY || ''
                };
            }
            
            return new Response(JSON.stringify(config), { headers });
        } catch (error) {
            return new Response(JSON.stringify({ error: 'Failed to load config' }), {
                status: 500,
                headers
            });
        }
    }
    
    // Handle POST request
    if (request.method === 'POST') {
        try {
            const data = await request.json();
            
            // Validate
            if (!data.pexels_api_key || !data.pixabay_api_key) {
                return new Response(JSON.stringify({ error: 'Both API keys are required' }), {
                    status: 400,
                    headers
                });
            }
            
            // Save to KV storage
            await env.KV_NAMESPACE.put('config', JSON.stringify(data));
            
            return new Response(JSON.stringify({ success: true, message: 'Configuration saved successfully!' }), {
                headers
            });
        } catch (error) {
            return new Response(JSON.stringify({ error: 'Failed to save config' }), {
                status: 500,
                headers
            });
        }
    }
    
    // Method not allowed
    return new Response(JSON.stringify({ error: 'Method not allowed' }), {
        status: 405,
        headers
    });
}
