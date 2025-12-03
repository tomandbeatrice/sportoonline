import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import CareerOpportunities from '@/components/marketplace/CareerOpportunities.vue'

describe('CareerOpportunities', () => {
  it('renders job listings correctly', () => {
    const wrapper = mount(CareerOpportunities)
    
    // Check that the component renders
    expect(wrapper.exists()).toBe(true)
    
    // Check that the header is present
    expect(wrapper.text()).toContain('Kariyer Fırsatları')
    
    // Check that job cards are rendered
    const jobCards = wrapper.findAll('.job-card')
    expect(jobCards.length).toBeGreaterThan(0)
  })

  it('displays correct number of job openings', () => {
    const wrapper = mount(CareerOpportunities)
    
    // Check the job count in the header badge
    expect(wrapper.text()).toContain('Açık Pozisyon')
    
    // Verify we have job cards
    const jobCards = wrapper.findAll('.job-card')
    expect(jobCards.length).toBe(12) // Based on the mock data
  })

  it('opens application form when "Başvur" button is clicked', async () => {
    const wrapper = mount(CareerOpportunities)
    
    // Initially, the form should not be visible
    expect(wrapper.find('.fixed.inset-0').exists()).toBe(false)
    
    // Find and click the first "Başvur" button
    const applyButton = wrapper.find('button')
    await applyButton.trigger('click')
    
    // Now the form should be visible
    expect(wrapper.vm.showApplicationForm).toBe(true)
  })

  it('closes application form when cancel button is clicked', async () => {
    const wrapper = mount(CareerOpportunities)
    
    // Open the form first
    wrapper.vm.openApplicationForm(wrapper.vm.jobOpenings[0])
    await wrapper.vm.$nextTick()
    
    expect(wrapper.vm.showApplicationForm).toBe(true)
    
    // Close the form
    wrapper.vm.closeApplicationForm()
    await wrapper.vm.$nextTick()
    
    expect(wrapper.vm.showApplicationForm).toBe(false)
  })

  it('validates required form fields', async () => {
    const wrapper = mount(CareerOpportunities)
    
    // Open application form
    wrapper.vm.openApplicationForm(wrapper.vm.jobOpenings[0])
    await wrapper.vm.$nextTick()
    
    // Try to submit without filling required fields
    const form = wrapper.find('form')
    
    // Check that form has required fields
    const nameInput = wrapper.find('#applicant-name')
    const emailInput = wrapper.find('#applicant-email')
    
    expect(nameInput.attributes('required')).toBeDefined()
    expect(emailInput.attributes('required')).toBeDefined()
  })

  it('handles resume file upload', async () => {
    const wrapper = mount(CareerOpportunities)
    
    // Open application form
    wrapper.vm.openApplicationForm(wrapper.vm.jobOpenings[0])
    await wrapper.vm.$nextTick()
    
    // Mock file upload
    const file = new File(['resume content'], 'resume.pdf', { type: 'application/pdf' })
    const fileInput = wrapper.find('#applicant-resume')
    
    // Simulate file selection
    Object.defineProperty(fileInput.element, 'files', {
      value: [file],
      writable: false
    })
    
    await fileInput.trigger('change')
    
    // Check that filename is stored
    expect(wrapper.vm.applicationForm.resumeFileName).toBe('resume.pdf')
  })

  it('submits application successfully', async () => {
    const wrapper = mount(CareerOpportunities)
    
    // Open application form
    wrapper.vm.openApplicationForm(wrapper.vm.jobOpenings[0])
    await wrapper.vm.$nextTick()
    
    // Fill out the form
    wrapper.vm.applicationForm.name = 'Test User'
    wrapper.vm.applicationForm.email = 'test@example.com'
    wrapper.vm.applicationForm.phone = '+90 555 123 45 67'
    wrapper.vm.applicationForm.resumeFileName = 'resume.pdf'
    wrapper.vm.applicationForm.message = 'I am interested in this position'
    
    // Submit the form
    await wrapper.vm.submitApplication()
    
    // Wait for async operation
    await new Promise(resolve => setTimeout(resolve, 1600))
    
    // Check that the form is closed
    expect(wrapper.vm.showApplicationForm).toBe(false)
    
    // Check that success toast is shown
    expect(wrapper.vm.showSuccessToast).toBe(true)
  })

  it('renders job details correctly', () => {
    const wrapper = mount(CareerOpportunities)
    
    const firstJob = wrapper.vm.jobOpenings[0]
    
    // Check that the first job details are rendered
    expect(wrapper.text()).toContain(firstJob.title)
    expect(wrapper.text()).toContain(firstJob.department)
    expect(wrapper.text()).toContain(firstJob.location)
  })

  it('displays job tags correctly', () => {
    const wrapper = mount(CareerOpportunities)
    
    const firstJob = wrapper.vm.jobOpenings[0]
    
    // Check that tags are rendered
    firstJob.tags.forEach(tag => {
      expect(wrapper.text()).toContain(tag)
    })
  })
})
