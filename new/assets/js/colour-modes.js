/*!
 * Color mode toggler for Bootstrap Icons version
 */

(() => {
  'use strict'

  const getStoredTheme = () => localStorage.getItem('theme')
  const setStoredTheme = theme => localStorage.setItem('theme', theme)

  const getPreferredTheme = () => {
	const storedTheme = getStoredTheme()
	if (storedTheme) return storedTheme
	return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
  }

  const setTheme = theme => {
	if (theme === 'auto') {
	  document.documentElement.setAttribute(
		'data-bs-theme',
		window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
	  )
	} else {
	  document.documentElement.setAttribute('data-bs-theme', theme)
	}
  }

  setTheme(getPreferredTheme())

  const showActiveTheme = (theme, focus = false) => {
	const themeSwitcher = document.querySelector('#bd-theme')
	if (!themeSwitcher) return

	const themeSwitcherText = document.querySelector('#bd-theme-text')
	const activeThemeIcon = themeSwitcher.querySelector('i')
	const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
	const iconOfActiveBtn = btnToActive.querySelector('i.bi:not(.bi-check2)').className

	// Reset all buttons
	document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
	  element.classList.remove('active')
	  element.setAttribute('aria-pressed', 'false')
	  const checkIcon = element.querySelector('.bi-check2')
	  if (checkIcon) checkIcon.classList.add('d-none')
	})

	// Mark the active one
	btnToActive.classList.add('active')
	btnToActive.setAttribute('aria-pressed', 'true')
	const checkIcon = btnToActive.querySelector('.bi-check2')
	if (checkIcon) checkIcon.classList.remove('d-none')

	// Update the main toggle icon
	activeThemeIcon.className = iconOfActiveBtn

	// Update accessibility label
	const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`
	themeSwitcher.setAttribute('aria-label', themeSwitcherLabel)

	if (focus) themeSwitcher.focus()
  }

  // React to system theme changes if in auto mode
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
	const storedTheme = getStoredTheme()
	if (storedTheme !== 'light' && storedTheme !== 'dark') {
	  setTheme(getPreferredTheme())
	  showActiveTheme(getPreferredTheme())
	}
  })

  // Setup on load
  window.addEventListener('DOMContentLoaded', () => {
	const currentTheme = getPreferredTheme()
	showActiveTheme(currentTheme)

	document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
	  toggle.addEventListener('click', () => {
		const theme = toggle.getAttribute('data-bs-theme-value')
		setStoredTheme(theme)
		setTheme(theme)
		showActiveTheme(theme, true)
	  })
	})
  })
})()