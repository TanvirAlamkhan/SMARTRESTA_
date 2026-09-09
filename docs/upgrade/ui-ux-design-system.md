# SMARTRESTA — Premium UI/UX Design System Specification

## Design Philosophy
SMARTRESTA is designed as a modern, high-performance, restaurant-focused SaaS operating system. It combines the sleek aesthetic of state-of-the-art web dashboards with the ergonomic, touch-friendly requirements of fast-paced restaurant POS and Kitchen Display Systems.

## Central Design Tokens & Variables (`assets/css/variables.css`)

### Color System
- **Light Theme Backgrounds**: `--bg-primary` (`#F8F9FA`), `--bg-secondary` (`#F1F3F5`), `--surface` (`#FFFFFF`)
- **Dark Theme Backgrounds**: `--bg-primary` (`#0F1115`), `--bg-secondary` (`#16191E`), `--surface` (`#1C2026`)
- **Accent Brand Palette**: Warm Brass/Amber (`--accent`: `#C58B4E`, `--accent-hover`: `#B0783D`, `--accent-dark`: `#8C5B28`)
- **Status Indicators**:
  - **Success**: `#10B981` (Completed, Settled, Active, Ready)
  - **Warning**: `#F59E0B` (Pending, Unpaid, Open, Preparing)
  - **Danger**: `#EF4444` (Cancelled, Voided, Refunded, Delayed)
  - **Info**: `#3B82F6` (Submitted, Routed, Digital MFS)

### Typography & Hierarchy (`assets/css/typography.css`)
- **Font Family**: `'Segoe UI', system-ui, -apple-system, sans-serif`
- **Scale**:
  - `Page Title`: `1.5rem` (`24px`), Bold `700`
  - `Section Title`: `1.25rem` (`20px`), SemiBold `600`
  - `Card Header`: `1.05rem` (`16.8px`), SemiBold `600`
  - `Body / Labels`: `0.9rem` (`14.4px`), Regular `400` / Medium `500`
  - `KDS Ticket Headers`: `1.15rem` (`18.4px`), ExtraBold `800`

### Elevation & Shadows
- `--shadow-sm`: `0 1px 2px 0 rgba(0, 0, 0, 0.05)`
- `--shadow-md`: `0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.04)`
- `--shadow-lg`: `0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03)`
- `--shadow-drawer`: `0 -4px 20px rgba(0, 0, 0, 0.15)`

### Component Geometry
- `--radius-sm`: `6px` (Badges, Buttons)
- `--radius-md`: `10px` (Cards, Input Fields, Modals)
- `--radius-lg`: `14px` (Drawer Panels, KDS Cards)
- `--radius-full`: `9999px` (Pills, Status Badges)
