@props([
    'rows' => 6,
    'cols' => 5,
])

<div class="table-shell">
    <div class="overflow-x-auto">
        <table class="table-ui">
            <thead>
                <tr>
                    @for($c=0;$c<$cols;$c++)
                        <th><div class="skeleton h-4 w-24"></div></th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @for($r=0;$r<$rows;$r++)
                    <tr>
                        @for($c=0;$c<$cols;$c++)
                            <td>
                                <div class="skeleton h-4 w-full"></div>
                            </td>
                        @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
