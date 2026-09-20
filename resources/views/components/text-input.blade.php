@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#920a69] focus:ring-[#920a69] rounded-lg shadow-sm']) }}>
