# SMARTRESTA — Responsive Design Audit & Breakpoint Matrix

## Tested Viewport Resolution Breakpoints

| Viewport Range | Target Device | Navigation Strategy | Layout Adaptation | Status |
| :--- | :--- | :--- | :--- | :--- |
| **>= 1280px** | Large Desktop / Workstation | Fixed Left Sidebar (`260px`) + Sticky Topbar | 4-Column KPI Grid, 4-Column KDS Board, 2-Column POS Layout | PASSED |
| **1024px – 1279px** | Laptop / iPad Pro | Collapsed Left Sidebar (`220px`) | 2-Column / 3-Column Grids, POS cart fixed right (`320px`) | PASSED |
| **768px – 1023px** | Tablet (iPad / Touch Terminal) | Off-canvas Drawer Sidebar + Touch Targets | 2-Column Grids, Horizontal Scroll Data Tables, Touch POS Grid | PASSED |
| **320px – 767px** | Mobile Smartphone (iOS / Android) | Mobile Bottom Nav Bar (`64px`) + Slide-in Drawer | Single-Column Stack, POS Slide-up Cart Sheet, Responsive Table Cards | PASSED |

## Component Adaptations

### 1. Mobile POS Cart
On mobile viewports (< 768px), the Waiter POS cart transforms into a bottom slide-up sheet (`pos-cart.mobile-expanded`) with fixed checkout summary bar.

### 2. KDS Kanban Columns
On mobile screens, the 4-column KDS Kanban board collapses into a single-column scrollable feed with large status transition buttons.

### 3. Data Tables
Wide tables (`.data-table`) feature container horizontal scrolling (`.table-container`) preventing text truncation or layout clipping.
