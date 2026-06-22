/**
 * API endpoint for managing configuration
 * GET: Returns current config
 * POST: Updates config
 */

export async function onRequest(context) {
    const { request, env } = context;
    
    // Handle GET request
    if (request.method === 'GET') {
        try {
            // Get config from KV storage
            let config = await env.KV_NAMESPACE.get('config', 'json');
            
            if (!config) {
                // Default config if not set
                config = {
                    pexels_api_key: env.PEXELS_API_KEY || 'hPfLL2XaPl3rVFEHXNaQbZstXrX1vZMSxmuvN9tqrAwbpXSZhdVL3Blm',
                    pixabay_api_key: env.PIXABAY_API_KEY || '56395196-037a4e0daa26799bb7627b4f3'
                };
            }
            
            return new Response(JSON.stringify(config), {
                headers: {
                    'Content-Type': 'application/json',
                    'Access-Control-Allow-Origin': '*'
                }
            });
        } catch (error) {
            return new Response(JSON.stringify({ error: 'Failed to load config' }), {
                status: 500,
                headers: { 'Content-Type': 'application/json' }
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
                    headers: { 'Content-Type': 'application/json' }
                });
            }
            
            // Save to KV storage
            await env.KV_NAMESPACE.put('config', JSON.stringify(data));
            
            return new Response(JSON.stringify({ success: true }), {
                headers: {
                    'Content-Type': 'application/json',
                    'Access-Control-Allow-Origin': '*'
                }
            });
        } catch (error) {
            return new Response(JSON.stringify({ error: 'Failed to save config' }), {
                status: 500,
                headers: { 'Content-Type': 'application/json' }
            });
        }
    }
    
    // Method not allowed
    return new Response(JSON.stringify({ error: 'Method not allowed' }), {
        status: 405,
        headers: { 'Content-Type': 'application/json' }
    });
}
