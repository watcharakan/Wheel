import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'

const storeNav = [
  {
    title: 'Home',
    to: { name: 'second-page' },
    icon: { icon: 'bx-home-alt' },
  },
  {
    title: 'Project',
    to: { name: 'project' },
    icon: { icon: 'bx-send' },
  },
  {
    title: 'Customer Management',
    to: { name: 'customer' },
    icon: { icon: 'bx-user' },
  },
  {
    title: 'Prize Management',
    to: { name: 'prize' },
    icon: { icon: 'bx-receipt' },
  },
]

const adminExtra = [
  {
    title: 'Package Management',
    to: { name: 'packagemanagement' },
    icon: { icon: 'bx-box' },
  },
  {
    title: 'User',
    to: { name: 'user' },
    icon: { icon: 'bx-user' },
  },
]

export default function useVerticalNavigation() {
  const { role } = storeToRefs(useAuthStore())
  return role.value === 'admin' ? [...storeNav, ...adminExtra] : storeNav
}
