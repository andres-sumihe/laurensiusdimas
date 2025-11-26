import asyncio
from playwright import async_api
from playwright.async_api import expect

async def run_test():
    pw = None
    browser = None
    context = None
    
    try:
        # Start a Playwright session in asynchronous mode
        pw = await async_api.async_playwright().start()
        
        # Launch a Chromium browser in headless mode with custom arguments
        browser = await pw.chromium.launch(
            headless=True,
            args=[
                "--window-size=1280,720",         # Set the browser window size
                "--disable-dev-shm-usage",        # Avoid using /dev/shm which can cause issues in containers
                "--ipc=host",                     # Use host-level IPC for better stability
                "--single-process"                # Run the browser in a single process mode
            ],
        )
        
        # Create a new browser context (like an incognito window)
        context = await browser.new_context()
        context.set_default_timeout(5000)
        
        # Open a new page in the browser context
        page = await context.new_page()
        
        # Navigate to your target URL and wait until the network request is committed
        await page.goto("http://localhost:8000", wait_until="commit", timeout=10000)
        
        # Wait for the main page to reach DOMContentLoaded state (optional for stability)
        try:
            await page.wait_for_load_state("domcontentloaded", timeout=3000)
        except async_api.Error:
            pass
        
        # Iterate through all iframes and wait for them to load as well
        for frame in page.frames:
            try:
                await frame.wait_for_load_state("domcontentloaded", timeout=3000)
            except async_api.Error:
                pass
        
        # Interact with the page elements to simulate user flow
        # -> Navigate to admin login page to login as admin and create draft project and client logo
        await page.goto('http://localhost:8000/admin', timeout=10000)
        await asyncio.sleep(3)
        

        # -> Input admin email and password, then click Sign in button
        frame = context.pages[-1]
        # Input admin email
        elem = frame.locator('xpath=html/body/div/div/main/div/section/form/div/div/div/div/div[2]/div/div/input').nth(0)
        await page.wait_for_timeout(3000); await elem.fill('hello@laurensiusdimas.com')
        

        frame = context.pages[-1]
        # Input admin password
        elem = frame.locator('xpath=html/body/div/div/main/div/section/form/div/div[2]/div/div/div[2]/div/div/input').nth(0)
        await page.wait_for_timeout(3000); await elem.fill('password')
        

        frame = context.pages[-1]
        # Click Sign in button
        elem = frame.locator('xpath=html/body/div/div/main/div/section/form/div[2]/div/button').nth(0)
        await page.wait_for_timeout(3000); await elem.click(timeout=5000)
        

        # -> Navigate to Projects section to create a new draft project
        frame = context.pages[-1]
        # Click Projects in admin menu
        elem = frame.locator('xpath=html/body/div/aside/nav/ul/li/ul/li[3]/a').nth(0)
        await page.wait_for_timeout(3000); await elem.click(timeout=5000)
        

        # -> Click the 'New project' button to start creating a new draft project
        frame = context.pages[-1]
        # Click 'New project' button
        elem = frame.locator('xpath=html/body/div/div/main/div/section/header/div[2]/div/a').nth(0)
        await page.wait_for_timeout(3000); await elem.click(timeout=5000)
        

        # -> Fill in the project title, slug, category, section, layout, description, and set visibility to draft
        frame = context.pages[-1]
        # Input project title
        elem = frame.locator('xpath=html/body/div/div/main/div/section/div/div/form/div/div/section/div/div/div/div/div/div/div[2]/div/div/input').nth(0)
        await page.wait_for_timeout(3000); await elem.fill('Draft Project Example')
        

        # -> Complete filling slug, category, description, set visibility to draft, and submit the form
        frame = context.pages[-1]
        # Input project slug
        elem = frame.locator('xpath=html/body/div/div/main/div/section/div/div/form/div/div/section/div/div/div/div[2]/div/div/div[2]/div/div/input').nth(0)
        await page.wait_for_timeout(3000); await elem.fill('draft-project-example')
        

        frame = context.pages[-1]
        # Input project category
        elem = frame.locator('xpath=html/body/div/div/main/div/section/div/div/form/div/div/section/div/div/div/div[3]/div/div/div[2]/div/div/input').nth(0)
        await page.wait_for_timeout(3000); await elem.fill('Example Category')
        

        frame = context.pages[-1]
        # Input project description
        elem = frame.locator('xpath=html/body/div/div/main/div/section/div/div/form/div/div/section/div/div/div/div[6]/div/div/div[2]/div/div/div/trix-editor').nth(0)
        await page.wait_for_timeout(3000); await elem.fill('This is a draft project description for testing visibility.')
        

        # -> Toggle visibility switch to draft (unpublished) and click Create button to save the draft project
        frame = context.pages[-1]
        # Toggle visibility switch to draft (unpublished)
        elem = frame.locator('xpath=html/body/div/div/main/div/section/div/div/form/div/div[4]/section/div/div/div/div/div/div/div/label/button').nth(0)
        await page.wait_for_timeout(3000); await elem.click(timeout=5000)
        

        frame = context.pages[-1]
        # Click Create button to save the draft project
        elem = frame.locator('xpath=html/body/div/aside/div/header/div/a').nth(0)
        await page.wait_for_timeout(3000); await elem.click(timeout=5000)
        

        # --> Assertions to verify final state
        frame = context.pages[-1]
        try:
            await expect(frame.locator('text=Draft Project Example - Visible to Public')).to_be_visible(timeout=1000)
        except AssertionError:
            raise AssertionError('Test case failed: Draft projects and client logos should NOT be visible on the public site, but draft content was found visible.')
        await asyncio.sleep(5)
    
    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()
            
asyncio.run(run_test())
    