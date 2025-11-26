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
        # -> Login to admin dashboard to start the reorder test as per instructions
        await page.goto('http://localhost:8000/admin/login', timeout=10000)
        await asyncio.sleep(3)
        

        # -> Input email and password, then click Sign in button to login to admin dashboard.
        frame = context.pages[-1]
        # Input the email address for login
        elem = frame.locator('xpath=html/body/div/div/main/div/section/form/div/div/div/div/div[2]/div/div/input').nth(0)
        await page.wait_for_timeout(3000); await elem.fill('hello@laurensiusdimas.com')
        

        frame = context.pages[-1]
        # Input the password for login
        elem = frame.locator('xpath=html/body/div/div/main/div/section/form/div/div[2]/div/div/div[2]/div/div/input').nth(0)
        await page.wait_for_timeout(3000); await elem.fill('password')
        

        frame = context.pages[-1]
        # Click the Sign in button to submit login form
        elem = frame.locator('xpath=html/body/div/div/main/div/section/form/div[2]/div/button').nth(0)
        await page.wait_for_timeout(3000); await elem.click(timeout=5000)
        

        # -> Click on 'Projects' link in the sidebar to navigate to project management section.
        frame = context.pages[-1]
        # Click on Projects link in sidebar to go to project management section
        elem = frame.locator('xpath=html/body/div/aside/nav/ul/li/ul/li[3]/a').nth(0)
        await page.wait_for_timeout(3000); await elem.click(timeout=5000)
        

        # -> Click the 'Reorder records' button to enable drag-and-drop reordering of projects.
        frame = context.pages[-1]
        # Click the 'Reorder records' button to enable drag-and-drop mode for projects
        elem = frame.locator('xpath=html/body/div/div/main/div/section/div/div/div/div/div/div/div/div/span/button').nth(0)
        await page.wait_for_timeout(3000); await elem.click(timeout=5000)
        

        # -> Perform drag-and-drop reorder of projects by swapping the first and last projects, then save the changes.
        frame = context.pages[-1]
        # Drag handle of the last project 'Order Sekarang' to the top position (simulate drag-and-drop)
        elem = frame.locator('xpath=html/body/div/div/main/div/section/div/div/div/div/div/div[3]/table/tbody/tr[4]/td/div/button').nth(0)
        await page.wait_for_timeout(3000); await elem.click(timeout=5000)
        

        frame = context.pages[-1]
        # Drag handle of the first project 'Hardy Motor' to the bottom position (simulate drag-and-drop)
        elem = frame.locator('xpath=html/body/div/div/main/div/section/div/div/div/div/div/div[3]/table/tbody/tr/td/div/button').nth(0)
        await page.wait_for_timeout(3000); await elem.click(timeout=5000)
        

        frame = context.pages[-1]
        # Click 'Finish reordering records' button to save the new project order
        elem = frame.locator('xpath=html/body/div/div/main/div/section/div/div/div/div/div/div/div/div/span/button').nth(0)
        await page.wait_for_timeout(3000); await elem.click(timeout=5000)
        

        # --> Assertions to verify final state
        frame = context.pages[-1]
        try:
            await expect(frame.locator('text=Drag-and-drop reorder successful').first).to_be_visible(timeout=1000)
        except AssertionError:
            raise AssertionError("Test case failed: Drag-and-drop reordering of projects and client logos did not persist on save or was not accurately reflected in the frontend display order as per the test plan.")
        await asyncio.sleep(5)
    
    finally:
        if context:
            await context.close()
        if browser:
            await browser.close()
        if pw:
            await pw.stop()
            
asyncio.run(run_test())
    